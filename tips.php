<?php

# requirements
require_once 'scripts/common.php';
require_once 'scripts/utilities.php';

# mobilize
ob_start( "mobilize" );

# prolog
echo '<?xml version="1.0"?>'."\r\n";

?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Donate to Tipr</title>
<?php headContent(); ?>
</head>
<body id="tipr-mobi">
<?php pageStart(); ?>
  <p>Tipr is free, but if you really like the Tipr service, consider tossing a tip our way. It will help us pay for bandwidth and keep this app free.</p>
  <p>Donations are accepted via Paypal</p>
  
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <p>
      <input type="hidden" name="cmd" value="_xclick" />
      <input type="hidden" name="business" value="aaron@easy-designs.net" />
      <input type="hidden" name="item_name" value="Tipr" />
      <input type="hidden" name="no_shipping" value="0" />
      <input type="hidden" name="no_note" value="1" />
      <input type="hidden" name="currency_code" value="USD" />
      <input type="hidden" name="tax" value="0" />
      <input type="hidden" name="lc" value="US" />
      <input type="hidden" name="bn" value="PP-DonationsBF" />
      <input type="image" src="/img/donate.png" border="0" name="submit" alt="Donate with PayPal - it's fast, free and secure!" />
      <img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" />
    </p>
  </form>
  
  <p>or Tipjoy</p>

  <p><script language="javascript" src="http://tipjoy.com/custombutton?targetUser=aarongustafson&#38;targetUrl=http://tipr.mobi&#38;bg=dark&#38;width=215"></script></p>
  
  <p>Thanks!</p>
  
<?php pageEnd(); ?>
</body>
</html>
<?php

# clear the buffer
ob_end_flush();

?>