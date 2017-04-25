<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'rabbitmq', 'rabbitmq');
$channel = $connection->channel();


$channel->exchange_declare('logs', 'fanout', false, false, false);


$data = ['data' => 'Hello'];
$json = json_encode($data); 

for ($i = 1; $i <= 50; $i++) {

  $info = " [$i] => $json ";
  // Cria a mensagem
  // delivery_mode=2: deixa a mensagem persistente
  $msg = new AMQPMessage($info, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

  // Publica a mensagem
  $channel->basic_publish($msg, 'logs','dtux');

  echo " [x] Sent $info\n";

}

$channel->close();
$connection->close();
