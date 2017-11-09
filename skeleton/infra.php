
<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
require_once __DIR__ . '/conn.php';
 

$channel->queue_delete('queue.sales.purchase');
$channel->queue_delete('queue.logging.logging');
$channel->queue_delete('queue.sales.admin');

$channel->queue_delete('queue.infra.elk');
$channel->queue_delete('queue.teacher.new');
$channel->exchange_delete('system.sales');
$channel->exchange_delete('system.logging');
$channel->exchange_delete('system.elk');


$channel->exchange_delete('system.teacher.notify');
// Primeiro passo: criar um exchange(repassa informações para outras filas)
$channel->exchange_declare('system.sales', 'direct', false, false, false); 
$channel->exchange_declare('system.logging', 'direct', false, false, false); 
$channel->exchange_declare('system.elk', 'direct', false, false, false); 
$channel->exchange_declare('system.teacher.notify', 'direct', false, false, false);

$channel->queue_declare('queue.teacher.new',
false,
true,
false,
false
);


$channel->queue_declare('queue.infra.elk',
false,
true,
false,
false
);

// Segundo passo: criar uma fila principal(chegada da venda)
$channel->queue_declare('queue.sales.purchase',
false,
true,
false,
false
);
// Terceiro passo: criar as filas
$channel->queue_declare( 'queue.logging.logging',
false,
true,
false,
false
);
$channel->queue_declare( 'queue.sales.admin',
                         false,
                         true,
                         false,
                         false
);

//Determina quem processará a mensagem, pode ser uma ou mais filas 
$channel->queue_bind('queue.sales.purchase', 'system.sales');
$channel->queue_bind('queue.sales.admin', 'system.sales');


//Fila secundaria
$channel->queue_bind('queue.infra.elk', 'system.elk');

$channel->queue_bind('queue.teacher.new', 'system.teacher.notify');
// Fazendo com q um exchange enchergue outro, para manter o desacoplamento
// foi criado um exachange de log possibilitando centralizar as informações 
// e facilitar  a externalização desse log
$channel->exchange_bind('system.logging','system.sales');
$channel->exchange_bind('system.logging','system.elk');
$channel->exchange_bind('system.logging','system.teacher.notify');

$channel->queue_bind('queue.logging.logging', 'system.logging'); 

$channel->close();
$connection->close(); 
