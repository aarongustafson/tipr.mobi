(function( window, navigator, $document ){
	// Register the service worker
	if ( 'serviceWorker' in navigator )
	{
		var $update = document.createElement('button'),
				newWorker;
		
		$update.className = 'toast toast--update';
		$update.hidden = true;
		$update.innerHTML = 'App update available. Click to reload.';
		$update.addEventListener('click', function(){
			newWorker.postMessage({ action: 'skipWaiting' });
		});
		$document.body.appendChild( $update );
		
		function showUpdateBar() {
			$update.hidden = false;
		}

		window.addEventListener('load', function(){
			navigator.serviceWorker
				.register( '/serviceworker.js' )
				.then(function(registration){
					registration.addEventListener('updatefound', function(){
						newWorker = registration.installing;
						newWorker.addEventListener('statechange', function(){
							if ( newWorker.state == 'installed' &&
									 navigator.serviceWorker.controller )
							{
								showUpdateBar();
							}
						}); // statechange
					}); // updatefound
				}); // then
		}); // load

		var refreshing;
		navigator.serviceWorker.addEventListener('controllerchange', function(){
			if (refreshing) return;
			window.location.reload();
			refreshing = true;
		});
	}
}( this, this.navigator, this.document ));