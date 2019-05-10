<?php
  $a = `/usr/bin/curl -s -L "http://icanhascheezburger.com/?random" | grep 'icanhascheezburger.files.wordpress.com' | grep 'img[^>]*src' | head -1 | sed 's/.*src="//;s/".*//'`;
  header( "Location: $a" );
?>
