<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require_once __DIR__ . '/conn.php';

for ($i = 1; $i <= 10; $i++) {
    
    $tags = ["system.sales","system.elk","system.teacher.notify"];
    shuffle($tags);
    $tag = reset($tags);
    $data = ['type' => $tag, 'body' => "pay the product id [$i]", 'date' => new DateTime(), 'value' => '100', 'id' => $i];
    $json = json_encode($data);

    $msg = new AMQPMessage($json, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
    $channel->basic_publish($msg, $tag);  
    usleep(5000000);

}
$channel->close();
$connection->close();
 