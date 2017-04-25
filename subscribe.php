<?php

//subscribe.php

function f($redis, $chan, $msg) {
    switch($chan) {
        case 'chan-1':
            print "get $msg from $chan\n";
            break;
        case 'chan-2':
            print "get $msg FROM $chan\n";
            break;
        case 'chan-3':
            print "get $msg FROM $chan\n";
            break;
        case 'chan-4':
            break;
    }
}

ini_set('default_socket_timeout', -1);

$redis = new Redis();
$redis->pconnect('localhost',6379);

$redis->subscribe(array('chan-1', 'chan-2', 'chan-3'), 'f');
print "\n";

?>
