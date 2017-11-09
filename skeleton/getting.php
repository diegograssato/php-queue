<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
 
require_once __DIR__ . '/conn.php';
function getQueue($queue){

  $connection = new AMQPStreamConnection('127.0.0.1', 5672, 'hitpay_user', 'hitpay_pass','logging');
  $channel = $connection->channel();

  echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";
  // Criar closure de resposta
  $callback = function($msg){ 
    echo ' [x] ', $msg->delivery_info['routing_key'], ':', $msg->body, "\n"; 
    //$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done", "\n"; 
  };

  // O segundo parametro '1' diz ao rabbit para nao processar mais que 1 por vez
  $channel->basic_qos(null, 1, null);

  // Indica o que será feito no canal 'task_queue' com a closure de resposta
  // assim que chegar a mensagem
  $channel->basic_consume($queue, '', false, true, false, false, $callback);
  $channel->confirm_select(true);

  // Fica escutando o canal 'dtux'
  while(count($channel->callbacks)) {
    try{
      $channel->wait();
    }
    catch(Exception $e)
    {
      echo 'Connection fail.';
    }
      
  }
  $channel->close();
  $connection->close();
}
$severities = array_slice($argv, 1);
if(empty($severities )) {
    file_put_contents('php://stderr', "Usage: $argv[0] [info] [warning] [error]\n");
    exit(1);
}
$tag = $severities[0];

getQueue($tag); 

