<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Abrindo conexão
$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'rabbitmq', 'rabbitmq');
$channel = $connection->channel();

$queueDefault = "carro";
$exchange = "carro";
$queueUsado = "carroUsado";
$queueNovo = "carroNovo";

// Removendo as filas e exchange para criarmos novamente
$channel->queue_delete($queueUsado);
$channel->queue_delete($queueNovo);
$channel->exchange_delete($exchange);

// Primeiro passo: criar um exchange
$channel->exchange_declare($exchange, 'topic', false, false, false);

// Segundo passo: criar uma fila principal
$channel->queue_declare($queueDefault, false, true, false, false, false,
                        array(
                          'x-message-ttl' => array('I', 20000),
                          'x-dead-letter-exchange' => array('S', '_exchange_to_default')
                        )
);

$channel->queue_declare($queueUsado, false, true);
$channel->queue_declare($queueNovo, false, true);


// Quarto passo: definir que, quando uma mensagem cair no exchange (ou seja, não pode ser processada)
// ela deverá ser direcionada para a fila de DQL
$channel->queue_bind($queueUsado, $exchange, $queueDefault);
$channel->queue_bind($queueNovo, $exchange, $queueDefault);

$channel->close();
$connection->close();

