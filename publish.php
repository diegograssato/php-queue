<?php

//publish.php
$redis = new Redis();

try{
  $redis->pconnect('localhost',6379);
  $redis->publish('chan-1', 'hello, world!'); // send message to channel 1.
  $redis->publish('chan-2', 'hello, world2!'); // send message to channel 2.
  $redis->publish('chan-3', 'hello, world3!'); // send message to channel 2.
}catch(\Exception $e){
  echo "==> " . $e->getMessage();
}
  print "\n";
  $redis->close();

?>
