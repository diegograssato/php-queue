<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Abrindo conexão
$connection = new AMQPStreamConnection('127.0.0.1', 5672, 'hitpay_user', 'hitpay_pass','logging');
$channel = $connection->channel();