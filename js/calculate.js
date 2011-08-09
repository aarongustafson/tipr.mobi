function calculator(){
  var request, form, check_field, percent_field, check, tip, total, loader, message, again;

  // init
  function init(){
    // make sure we have a form and can do XHR
    request = getXHR();
    if( !document.getElementById( 'calc' ) ||
        !request ){ return; }

    // form
    form = document.getElementById( 'calc' );

    // fields
    check_field   = document.getElementById( 'form-check' );
    percent_field = document.getElementById( 'form-percent' );

    // cells
    check    = document.getElementById( 'check' ).getElementsByTagName( 'td' )[0];
    tip      = document.getElementById( 'tip' ).getElementsByTagName( 'td' )[0];
    total    = document.getElementById( 'total' ).getElementsByTagName( 'td' )[0];
    total_th = document.getElementById( 'total' ).getElementsByTagName( 'th' )[0];

    // loader
    loader = document.createElement( 'div' );
    loader.className = 'loader';

    // message
    message = document.getElementsByTagName( 'p' )[0];
    messagetxt = 'Ok, here ya go:';
    
    // again
    again = document.createElement( 'p' );
    again.innerHTML = 'Wanna <a href="/">do another</a>?';

    // promo
    promo = document.createElement( 'p' );
    promo.setAttribute( 'id', 'promo' );

    // hijack
    form.onsubmit = function(){ query(); return false; };
  }

  // runs the Ajax query
  function query(){
    var check   = check_field.value;
    if( check == '' ){ return err(); }
    var percent = percent_field.value;
    showLoader();
    request.onreadystatechange = function(){
      parse( request );
    };
    request.open( 'POST', 'services/json.php', true );
    request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
    request.send( 'check='+check+'&percent='+percent );
  }

  // parses the response
  function parse( request ){
    if( request.readyState == 4 ){
      if( request.status == 200 || request.status == 304 ){
        hideLoader();
        var data = eval( '(' + request.responseText + ')' );
        check.innerHTML    = '$' + data.check;
        tip.innerHTML      = '$' + data.tip;
        total.innerHTML    = '$' + data.total;
        total_th.innerHTML = 'Total&nbsp;';
        if( data.message !== messagetxt ){
          messagetxt = data.message;
        }
        message.innerHTML = messagetxt;
        promo.innerHTML = data.promo;
        form.appendChild( again );
        form.appendChild( promo );
      }
    }
  }

  // error handler
  function err(){
    alert( 'You forgot to enter the check total' );
    total_field.focus();
    return false;
  }

  // loader controls
  function showLoader(){
    document.getElementsByTagName( 'body' )[0].appendChild( loader );
  }
  function hideLoader(){
    loader.parentNode.removeChild( loader );
  }

  // XHR
  function getXHR(){
    var xhr = false;
    if( window.XMLHttpRequest ){
      xhr = new XMLHttpRequest();
    }else if( window.ActiveXObject ){
      try{
        xhr = new ActiveXObject( 'Msxml2.XMLHTTP' );
      }catch( e ){
        try{
          xhr = new ActiveXObject( 'Microsoft.XMLHTTP' );
        }catch( e ){
          xhr = false;
        }
      }
    }
    return xhr;
  }

  // vroom
  init();

}
if( document.getElementById &&
    document.getElementsByTagName ){
  var calc = new calculator();
}