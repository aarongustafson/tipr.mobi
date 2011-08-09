<?php

# requirements
require_once 'scripts/common.php';
require_once 'scripts/utilities.php';

# mobilize
ob_start( "mobilize" );

# prolog
echo '<?xml version="1.0"?>'."\r\n";

$show = 'error';
if( $_REQUEST['hash'] ){
  # get the array
  $array = unserialize( base64_decode( $_REQUEST['hash'] ) );
  
  # verify that they won
  if( isWinner( $array['info'],
                $array['tip'],
                $array['total'] ) == $array['number'] ){
    $show = 'form';
  } else {
    $show = 'bad';
  }
}
if( $_REQUEST['name'] ){
  # process the form
  $fields = array( 'name', 'email', 'paypal', 'hash' );
  $info = array();
  foreach( $fields as $field ){
    $info[$field] = sanitize( $_REQUEST[$field] );
  }
  if( !processWinner( $info ) ){
    $show = 'error';
  } else {
    $show = 'thanks';
  }
}

switch( $show ){
  case 'form':
    $title = 'Congratulations, we&#8217;re buying your meal!';
    break;
  case 'bad':
    $title = 'Something&#8217;s fishy.';
    break;
  case 'thanks':
    $title = 'Thanks!';
    break;
  default:
    $title = 'We experienced an error.';
    break;
}

?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?= $title ?></title>
<?php headContent(); ?>
</head>
<body id="tipr-mobi">
<?php pageStart(); ?>
  <p><strong><?= $title ?></strong></p>
<?php if( $show == 'form' ){ ?>
  <p>Please fill in the following details and we&#8217;ll get you some money.</p>
  <form action="<?= $_SERVER['SCRIPT_NAME'] ?>" method="post">
    <table id="calculator">
      <tbody>
        <tr>
          <th scope="row"><label for="winner-name">Name</label>&nbsp;</th>
          <td><input type="text" id="winner-name" name="name" inputmode="titleCase" /></td>
        </tr>
        <tr>
          <th scope="row"><label for="winner-email">Email</label>&nbsp;</th>
          <td><input type="text" id="winner-email" name="email" /></td>
        </tr>
        <tr>
          <th></th>
          <td>
            <label for="winner-paypal-yes"><input type="radio" id="winner-paypal-yes" name="paypal" value="1" /> This <strong>is</strong> my Paypal email (faster)</label>
            <label for="winner-paypal-no"><input type="radio" id="winner-paypal-no" name="paypal" value="0" /> This <strong>is not</strong> my Paypal email</label>
          </td>
        </tr>
        <tr>
          <th></th>
          <td>
            <input type="hidden" name="hash" value="<?= $_REQUEST['hash'] ?>" />
            <input id="form-submit" type="submit" name="submit" value="Submit" />
          </td>
        </tr>
      </tbody>
    </table>
  </form>
<?php }elseif( $show == 'thanks' ){ ?>
  <p>We&#8217;ve received your info and will <?= ( $_REQUEST['paypal'] == '1' ) ? 'send your money shortly'
                                                                                : 'be in touch shortly' ?>.</p>
<?php }elseif( $show = 'bad' ){ ?>
  <p>It doesn&#8217;t look like you&#8217;re a winner. If you&#8217;re convinced you&#8217;ve received this message in error, please feel free to <a href="mailto:info@tipr.mobi">contact us</a> and we can verify your tip against our records. Just be sure to include the check total, tip amount, total paid, and date of the transaction.</p>
<?php } else { ?>
  <p>Something went horribly wrong. Please <a href="mailto:info@tipr.mobi">contact us</a> and we can verify your tip against our records. Just be sure to include the check total, tip amount, total paid, and date of the transaction.</p>
<?php } ?>
<?php pageEnd(); ?>
</body>
</html>
<?php

# clear the buffer
ob_end_flush();

?>