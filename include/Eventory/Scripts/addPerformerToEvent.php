<?php

require_once __DIR__ . '/../bootstrap.php';

if ($argc <= 2){
	printf("Usage:%s <fname> <lname> <eventId>\n", __FILE__);
	exit(-1);
}

$name = ucfirst(strtolower($argv[1])) . ' '. ucfirst(strtolower($argv[2]));
echo "adding [$name]\n";

$store = getStoreProvider();
$performer = $store->createPerformer($name);
print_R($performer);

$eventId = $argv[3];
if (intval($eventId) != $eventId){
	printf("invalid event id [%s]\n", $eventId);
	exit(-1);
}
$events = $store->loadEventsById(array($eventId));
if (count($events) != 1){
	printf("event not found [%s]\n", $eventId);
	exit(-1);
}
$event = reset($events);

$performer = $store->createPerformer($name);
$store->addPerformerToEvent($performer, $event);
