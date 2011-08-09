<?php

# requirements
require_once 'scripts/common.php';
require_once 'scripts/utilities.php';

# mobilize
ob_start( "mobilize" );

# defaults
$show = 'form';
$err  = false;

if( !empty( $_REQUEST['submit'] ) ){
  #required
  $required = Array( 'check' );
  # set up the errors
  $_REQUEST['errors'] = Array();
  # show the request
  showMe( $_REQUEST, '$_REQUEST' );
  # check for errors
  foreach( $required as $r ){
    if( empty( $_REQUEST[$r] ) ){
      $err = '<li>Not every required field was filled in. Fields with errors are marked.</li>';
      array_push( $_REQUEST['errors'], $r );
    }
  }
  # if there are no errors, run the query
  if( !$err ){
    # get the tip & total
    $_REQUEST['medium'] = 'P';
    $results = process( $_REQUEST );
    # show the thank you
    $show = 'results';
    # headers
    header( 'Cache-Control: no-cache' );
  } else {
    # show the form in case it was user error
    $show = 'form';
  }
}

?>
<!DOCTYPE html>
<html id="tipr_mobi">
<head>
  <title>Tipr</title>
<?php headContent(); ?>
</head>
<body id="tipr-mobi">
<?php pageStart(); ?>
<?php if( $show == 'form' ){
      # the form
        if( !$err ){ ?>
 <p>Enter the check amount &#38; how much you want to tip.</p>
<?php   } else { ?>
 <p class="error">There was an error with your submission: <em>you forgot to enter the check total</em>.</p>
<?php   } ?>
  <form id="calc" method="post" action="<?=$_SERVER['SCRIPT_NAME']?>">
    <fieldset>
      <?= ( $GLOBALS['DEBUG'] == 1 ) ? '<input type="hidden" name="debug" value="1" />' : '' ?>
      <table id="calculator">
        <tr id="check"<?= ( $err ) ? ' class="error"' : '' ?>>
          <th scope="row"><label for="form-check">Check</label> </th>
          <td><input type="number" id="form-check" name="check" min="0.00" autofocus="" inputmode="latin digits" placeholder="12.39" /></td>
        </tr>
        <tr id="tip">
          <th scope="row"><label for="form-percent">Tip</label> </th>
          <td>
            <select id="form-percent" name="percent">
              <option value=".05">5%</option>
              <option value=".1">10%</option>
              <option value=".15">15%</option>
              <option value=".2" selected="selected">20%</option>
              <option value=".25">25%</option>
              <option value=".3">30%</option>
              <option value=".35">35%</option>
              <option value=".4">40%</option>
              <option value=".45">45%</option>
              <option value=".5">50%</option>
            </select>
          </td>
        </tr>
        <tr id="total">
          <th></th>
          <td>
            <input id="form-submit" type="submit" name="submit" value="Submit" />
          </td>
        </tr>
      </table>
    </fieldset>
  </form>
<?php }elseif( $show == 'results' ){ ?>
  <p><?= $results['message'] ?></p>
  <table id="calculator">
    <tbody>
      <tr id="check">
        <th scope="row">Check&nbsp;</th>
        <td>$<?= $results['check'] ?></td>
      </tr>
      <tr id="tip">
        <th scope="row">Tip&nbsp;</th>
        <td>$<?= $results['tip'] ?></td>
      </tr>
      <tr id="total">
        <th scope="row">Total&nbsp;</th>
        <td>$<?= $results['total'] ?></td>
      </tr>
    </tbody>
  </table>
  <p>Wanna <a href="<?= $_SERVER['SCRIPT_NAME'] ?>">do another</a>?</p>
<?php   promo();
      } else {
        # serious error - pray it never happens ;-)
        error_log( 'Contact: '.$result['error'] ) ?>
  <p>Oops, something went horribly wrong, but we're looking into it. Please try again later.</p>
<?php } ?>
<?php pageEnd(); ?>
  <script type="text/javascript" src="/scripts/js_compress.php?url=/js/calculate.js&#38;pack"></script>
</body>
</html>
<?php

# clear the buffer
ob_end_flush();

?>