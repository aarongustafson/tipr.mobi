<?php

# get the script
$file = $_REQUEST['url'];
if( substr( $file, 0, 1 ) == '/' ) $file = $_SERVER['DOCUMENT_ROOT'] . $file;
$script = file_get_contents( $file );

# pack or not
$pack = !isset( $_REQUEST['pack'] ) ? false : true;

if( strlen( $script ) > 500 && $pack ){
  require 'classes/JavaScriptPacker.php';
  $packer = new JavaScriptPacker( $script );
  $script = $packer->pack();
} else {
  # remove comments
  $script = preg_replace( '/\/\*.*?\*\//i', '', $script );
  $script = preg_replace( '/\/\/.*?[\n\r]+?/i', '', $script );
  # remove new lines & tabs
  $script = preg_replace( '/[\n\r\t]+/', '', $script );
  # remove extra whitespace
  $script = preg_replace( '/\s{2,}/', ' ', $script );
}

# headers
header( 'Content-Type: application/javascript; charset=utf-8' );

# print
echo $script;

?>
