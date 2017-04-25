<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

// Cria a conexão
$connection = new AMQPStreamConnection('rabbitmq', 5672, 'rabbitmq', 'rabbitmq');

// Instancia o canal
$channel = $connection->channel();


$channel->exchange_declare('logs', 'fanout', false, false, false);

// Adiciona o canal
list($queue_name, ,) = $channel->queue_declare("dtux", false, false, false, false);

$channel->queue_bind($queue_name, 'logs');

// Printa na tela
echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

// Criar closure de resposta
$callback = function($msg){
  echo ' [x] ', $msg->body, "\n";
  sleep(substr_count($msg->body, '.'));
};

// O segundo parametro '1' diz ao rabbit para nao processar mais que 1 por vez
$channel->basic_qos(null, 1, null);

// Indica o que será feito no canal 'task_queue' com a closure de resposta
// assim que chegar a mensagem
$channel->basic_consume($queue_name, '', false, true, false, false, $callback);
$channel->confirm_select(true);

// Fica escutando o canal 'dtux'
while(count($channel->callbacks)) {
    $channel->wait();
}

// Fecha o canal
$channel->close();

// Fecha a conexão
$connection->close();
