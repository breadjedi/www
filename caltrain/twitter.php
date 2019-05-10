<?php
  $twitterFeed = 'http://twitter.com/statuses/user_timeline/10087032.json?count=50';
  $twitterFile = '/tmp/twitter_10087032.json';
  $twitterExpiry = 60;

  if ( !file_exists( $twitterFile ) || (time() - filemtime($twitterFile) > $twitterExpiry) ) {
    `curl -s $twitterFeed | perl /home/evang/bin/a.pl > $twitterFile`;
  }

  require $twitterFile;
?>
