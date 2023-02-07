const VERSION = 'v1:', // be sure to update ../_includes/js/register-serviceworker.js too

      // Stuff to load on install
      preinstall = [
        '/favicon.ico',
        '/'
      ],
      
      fetch_config = {
        images: {
          mode: 'no-cors'
        }
      };

self.addEventListener( 'activate', event => {
  
  // console.log('WORKER: activate event in progress.');
  
  // clean up stale caches
  event.waitUntil(
    caches.keys()
      .then( keys => {
        return Promise.all(
          keys
            .filter( key => {
              return ! key.startsWith( VERSION );
            })
            .map( key => {
              return caches.delete( key );
            })
        ); // end promise
      }) // end then
      .then( () => clients.claim() )
  ); // end event

});

self.addEventListener( 'message', event => {
  if ( event.data.action === 'skipWaiting' )
	{
    self.skipWaiting();
  }
});

self.addEventListener( 'fetch', event => {
	const request = event.request;

	if ( request.url.indexOf('chrome-extension://') === 0 )
	{
		event.respondWith(
			fetch( request )
		);
	}
	else
	{
		event.respondWith(
			// check the cache first
			caches.match( request )
				.then( cached_result => {
					if ( cached_result )
					{
						event.waitUntil(
							refreshCachedCopy( request )
						);
						return cached_result;
					}
					// fallback to network, but cache the result
					return fetch( request )
						.then( response => {
							const copy = response.clone();
							event.waitUntil(
								saveToCache( request, copy )
							); // end waitUntil
							return response;
						})
						// fallback to offline page
						.catch( () => respondWithOfflinePage() );
				})
		);
	}
});

self.addEventListener( 'install', function( event ){

  event.waitUntil(
    caches.open( VERSION )
      .then(function( cache ){
        return cache.addAll( preinstall );
      })
  );

});

function saveToCache( request, response )
{
  caches.open( VERSION )
    .then( cache => {
      return cache.put( request, response );
    });
}

function refreshCachedCopy( the_request )
{
  fetch( the_request )
    .then( the_response => {
      caches.open( VERSION )
        .then( the_cache => {
          return the_cache.put( the_request, the_response );
        });
    })
    .catch( () => respondWithOfflinePage() );
}

function respondWithOfflinePage()
{
  return caches.match( '/' )
           .catch( () => respondWithServerOffline() );
}

function respondWithServerOffline(){
  return new Response( '', {
    status: 408,
    statusText: 'Tipr appears to be offline.'
  });
}
