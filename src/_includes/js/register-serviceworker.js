(function( window, navigator, document ){
  // Register the service worker
  if ( "serviceWorker" in navigator )
  {
    window.sw_version = "v1:";

    window.addEventListener('load', function() {
      navigator.serviceWorker.register( "/serviceworker.js" );
    });
  }
}( this, this.navigator, this.document ));