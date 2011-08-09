<?php
/*------------------------------------------------------------------------------
   GLOBALS
------------------------------------------------------------------------------*/
define( 'SITE_NAME', 'Tipr' );
header( 'Content-Type: text/html; charset=utf-8' );

/* -----------------------------------------------------------------------------
 * Function: headContent()
 * Purpose:  Writes out the head of the document
 * ---------------------------------------------------------------------------*/
function headContent(){
  meta();
  css();
  js();
} # end headContent()

/* -----------------------------------------------------------------------------
 * Function: meta()
 * Purpose:  Writes out the common meta elements
 * ---------------------------------------------------------------------------*/
function meta(){ ?>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta http-equiv="Content-Language" content="en-us"/>
  <meta http-equiv="Cache-Control" content="max-age=86400"/>
  <meta name="Copyright" content="(c) <?= copyrightYear(); ?> Easy! Designs, LLC. All rights reserved."/>
  <meta name="apple-mobile-web-app-capable" content="yes"/>
  <link rel="apple-touch-icon" href="/appicon.png"/>
<?php } # end meta()

/* -----------------------------------------------------------------------------
 * Function: css()
 * Purpose:  Writes out the css for the site
 * ---------------------------------------------------------------------------*/
function css(){
  $css = Array( 'main.css' );
  $color = ( $GLOBALS['PINK'] == true ) ? 'pink.css' : 'color.css';
  array_push( $css, $color );
  echo '<style type="text/css"><!--';
  foreach( $css as $file ){
    echo file_get_contents( "{$_SERVER['DOCUMENT_ROOT']}/css/{$file}" );
  }
  echo '--></style>';
} # end css()

/* -----------------------------------------------------------------------------
 * Function: js()
 * Purpose:  Writes out the site JS files
 * ---------------------------------------------------------------------------*/
function js(){
  # no common js files right now
} # end js()

/* -----------------------------------------------------------------------------
 * Function: pageStart()
 * Purpose:  Writes out the top of each page
 * ---------------------------------------------------------------------------*/
function pageStart(){
  branding();
} # end pageStart()

/* -----------------------------------------------------------------------------
 * Function: branding()
 * Purpose:  Writes out the branding bit of pages
 * ---------------------------------------------------------------------------*/
function branding(){
  $logo = ( $GLOBALS['PINK'] == true ) ? 'logo-pink' : 'logo'; ?>
  <h1><img src="/img/<?=$logo?>.png" alt="Tipr" width="108" height="30" /></h1>
<?php } # end branding()

/* -----------------------------------------------------------------------------
 * Function: pageEnd()
 * Purpose:  Writes out the bottom of each page
 * ---------------------------------------------------------------------------*/
function pageEnd(){
  siteDetails();
  urchin();
} # end pageEnd()

/* -----------------------------------------------------------------------------
 * Function: nav()
 * Purpose:  Writes out the universal nav
 * ---------------------------------------------------------------------------*/
function nav(){ ?>
<ul id="nav">
  <li id="nav-faq"><a href="/about.php">Read the Tipr FAQ</a></li>
  <li id="nav-twitter"><a href="/twitter.php">On Twitter? Try Tipr TwitterBot</a></li>
  <li id="nav-tips"><a href="/tips.php">Like Tipr? Donate!</a></li>
</ul>
<?php } # end nav()

/* -----------------------------------------------------------------------------
 * Function: promo()
 * Purpose:  Writes out a promo for the site
 * ---------------------------------------------------------------------------*/
function promo(){ ?>
<p id="promo"><?= getPromo(); ?></p>
<?php } # end promo()

/* -----------------------------------------------------------------------------
 * Function: bookmarks()
 * Purpose:  Offers quick ways for folks to add Tipr to their favorites
 * ---------------------------------------------------------------------------*/
function bookmarks(){
  if( $_SERVER['SCRIPT_NAME'] == 'index.php' ){ ?>
<ul id="bookmarks">
  <li><a href="http://appmarks.com/appMarkIt.php?name=Tipr&#38;url=http%3A%2F%2Ftipr.mobi%2F"><img src="http://appmarks.com/images/zrkrAppmarkit.gif" style="border: 1px solid white;" alt="Add to AppMarks!" width="91" height="17" /></a></li>
  <li><a href="http://gridgets.com/add.php?widget=44"><img src="http://gridgets.com/images/gridgets-add-small.png" alt="Add to Grigets" width="90" height="20" /></a></li>
<?php if( $GLOBALS['PINK'] == true ){ ?>
  <li id="pink"><a href="http://pinkforoctober.org">Pink for October</a></li>
<?php } ?>
</ul>
<?php }
} # end bookmarks()

/* -----------------------------------------------------------------------------
 * Function: siteDetails()
 * Purpose:  Writes out the universal footer
 * ---------------------------------------------------------------------------*/
function siteDetails(){ ?>
<div id="footer">
  <p id="copyright">&#169; <?= copyrightYear() ?> <a href="http://easy-designs.net">Easy! Designs, LLC</a>. All rights reserved.</p>
<?php nav();
      bookmarks(); ?>
</div>
<?php } # end siteDetails()

/* -----------------------------------------------------------------------------
 * Function: copyrightYear()
 * Purpose:  Determines the copyright year
 * ---------------------------------------------------------------------------*/
function copyrightYear(){
  $year = date("Y");
  if($year != "2007"){
    $year = "2007-" . $year;
  }
  return $year;
} # end copyrightYear()

/* -----------------------------------------------------------------------------
 * Function: urchin()
 * Purpose:  Writes out Google Analytics code
 * ---------------------------------------------------------------------------*/
function urchin(){ ?>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
<script src="/scripts/js_compress.php?url=/js/urchin.js" type="text/javascript"></script>
<?php } # end urchin()

?>