<?php

use Eventory\Objects\Event\Event;
use Eventory\Storage\File\StorageProviderSerialized;
use Eventory\Storage\MySql\StorageProviderMySql;

$fileName = __DIR__ .'/slashdot.data';
$storeProviderFile = new StorageProviderSerialized($fileName);

$dbHost = 'localhost';
$dbUser = 'user';
$dbPass = 'pass';
$dbName = 'myDb';
$storeProviderDB = new StorageProviderMySql($dbHost, $dbUser, $dbPass, $dbName);

for ($i = 0; $i < 10000; $i++){
	$event = $storeProviderFile->loadEventById($i);
	if (!$event instanceof Event){
		continue;
	}

	$newEvent = $storeProviderDB->createEvent($event->eventUrl, $event->eventKey);
	$newEvent->description = $event->description;
	$newEvent->setCreated($event->getCreated());

	// TODO assets, sub-urls, performers


	$storeProviderDB->saveEvents(array($newEvent));
}