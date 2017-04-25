<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Abrindo conexão
$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'rabbitmq', 'rabbitmq');
$channel = $connection->channel();

// Removendo as filas e exchange para criarmos novamente

$channel->queue_delete('SaleCar');
$channel->queue_delete('Stock');
$channel->queue_delete('Financial');
$channel->exchange_delete('car');


// Primeiro passo: criar um exchange(repassa informações para outras filas)
$channel->exchange_declare('car', 'direct', false, true, false);

// Segundo passo: criar uma fila principal(chegada da venda)
$channel->queue_declare('SaleCar', false, true, false, false, false,
                        array(
                          'x-message-ttl' => array('I', 1000),
                          'x-dead-letter-exchange' => array('S', 'car'),
                        )
);
// Terceiro passo: criar as filas
$channel->queue_declare( 'Stock',
                         false,
                         true,
                         false,
                          true,
                          false
);
$channel->queue_declare( 'Financial',
                         false,
                         true,
                         false,
                         true
);

// Quarto passo: definir que, quando uma mensagem cair no exchange (ou seja, não pode ser processada)
// ela deverá ser direcionada para a fila de DQL
$channel->queue_bind('Stock', 'car', 'SaleCar', false);
$channel->queue_bind('Financial', 'car', 'SaleCar', false);

$data = ['data' => 'Hello'];
$json = json_encode($data);

$msg = new AMQPMessage($json, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
$channel->basic_publish($msg, '','SaleCar');

$channel->close();
$connection->close();

