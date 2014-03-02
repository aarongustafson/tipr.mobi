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
  <title>Tipr TwitterBot</title>
<?php headContent(); ?>
</head>
<body id="tipr-mobi">
<?php pageStart(); ?>
  <p>Tipr is now available as a Twitter service, which means you can use it via text message from your cell phone if you don't have a mobile web browser.</p>
  <p>How do you use it? Follow these simple steps:</p>
  <ol class="steps">
    <li>Befriend <a href="http://twitter.com/tipr">tipr</a>.</li>
    <li>Wait for Tipr to recprocate the friendship (about 30 seconds).</li>
    <li>Use Tipr's TwitterBot by sending it direct messages. For example <code>d tipr 10.25@20</code> will calculate a 20% tip on a $10.25 check.</li>
    <li>If you ever have questions <code>d tipr help</code>.</li>
  </ol>
  <p>Tipr TwitterBot could take up to 1 minute to respond to your query.</p>
  <p><strong>Note:</strong> if you are not getting text messages from Tipr, check your Twitter settings. You must have your account set up to receive Notifications from Tipr <em>and</em> you must have set your preferences such that Notifications are sent to your mobile.</p>
<?php pageEnd(); ?>
</body>
</html>
<?php

# clear the buffer
ob_end_flush();

?>