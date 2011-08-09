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
  <title>Tipr FAQ</title>
<?php headContent(); ?>
</head>
<body id="tipr-mobi">
<?php pageStart(); ?>
  <ul id="FAQ">
    <li>
      <h2>Why Tipr?</h2>
      <p>The name Tipr is an homage to Flickr, which we love.</p>
    </li>
    <li>
      <h2>Why Not Tippr?</h2>
      <p>Firstly, it's shorter to type in a mobile device. Secondly, since we already dropped the "e" we figured why bother with the duplicate "p." Thirdly, we can't spel.</p>
    </li>
    <li>
      <h2>Who Made Tipr?</h2>
      <p>Tipr is the brainchild of Aaron Gustafson. He really built it to make his own life easier, but decided to share it with his friends (and the rest of the world).</p>
    </li>
    <li>
      <h2>The tip isn't the percentage I specified. What gives?</h2>
      <p>Well, we make it so the total is always a palindrome.</p>
    </li>
    <li>
      <h2>Why is the total always a palindrome?</h2>
      <p>Palindrome's are cool. Also, they make it really easy to scan your credit card statements to see if anyone has manipulated your tip amount.</p>
    </li>
    <li>
      <h2>What's a palindrome?</h2>
      <p><a href="http://dictionary.reference.com/browse/palindrome">Look it up</a>.</p>
    </li>
    <li>
      <h2>How many ways are there to acess Tipr?</h2>
      <ol>
        <li><a href="/" title="Tipr is available on the web">On the web</a> (in old school or Ajaxified, depending on your browser&#8217;s capabilities).</li>
        <li><a href="/text.php" title="Tipr is available via text message">Via text message</a>.</li>
        <li><a href="/twitter.php" title="Tipr is available on Twitter">On Twitter</a></li>
      </ol>
    </li>
    <li>
      <h2>Is Tipr free?</h2>
      <p>Yes, Tipr is free and will remain so. If you find Tipr particularly useful, please consider <a href="/tips.php" title="Donate to Tipr">donating</a>.</p>
    </li>
  </ul>
<?php pageEnd(); ?>
</body>
</html>
<?php

# clear the buffer
ob_end_flush();

?>