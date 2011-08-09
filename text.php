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
  <title>Tipr via Text Messaging</title>
<?php headContent(); ?>
</head>
<body id="tipr-mobi">
<?php pageStart(); ?>
  <p>Tipr is now available as a text message service via the shortcode <em>41411</em>.</p>
  <p>How do you use it? Follow these simple steps:</p>
  <ol class="steps">
    <li>Begin a message to <em>41411</em>.</li>
    <li>Preface your message with <code>tipr</code>, followed by a space and then</li>
    <li>enter your bill and tip percentage in the following format: <code>bill@tip</code> (e.g. <code>tipr 10.25@20</code> will calculate a 20% tip on a $10.25 check).</li>
    <li>Tipr should respond within 30-40 seconds.</li>
  </ol>
  <p>If you ever have questions, send <code>tipr help</code> to <em>41411</em>.</p>
  <p><strong>Note:</strong> the cost of sending and recieving text messages varies by provider.</p>
<?php pageEnd(); ?>
</body>
</html>
<?php

# clear the buffer
ob_end_flush();

?>