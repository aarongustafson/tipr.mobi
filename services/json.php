<?php

# headers
header( 'Content-Type: application/json; charset=utf-8' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );

# requirements
require_once '../scripts/utilities.php';
require_once '../scripts/classes/FastJSON.php';


# cases
switch( $_REQUEST['service'] ){
  default:
    $_REQUEST['medium'] = 'A';
    break;
}

# process
$result = process( $_REQUEST );

# promo
$result['promo'] = getPromo();

# signature
$result['generator'] = 'Tipr';
$result['URI']       = 'http://tipr.mobi';

# echo the JSON response
echo FastJSON::encode( $result );

?>