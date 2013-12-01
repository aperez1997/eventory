<?php

use Eventory\Model\EventModel;

require_once __DIR__ . '/../bootstrap.php';

if ($argc < 3){
	printf("Usage: %s <performerId> <eventId>\n", __FILE__);
	exit();
}

$store = getStoreProvider();
$model = new EventModel($store);

$perfId = $argv[1];
$eventId = $argv[2];

$model->removePerformerIdFromEventId($perfId, $eventId);
