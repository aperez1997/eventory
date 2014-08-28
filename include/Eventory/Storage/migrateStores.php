<?php

use Eventory\Objects\Event\Event;
use Eventory\Storage\File\StorageProviderSerialized;
use Eventory\Storage\MySql\StorageProviderMySql;

if ($argc <= 1){
	printf("Usage:%s <dbpassword>\n", __FILE__);
	exit(-1);
}

$fileName = __DIR__ .'/slashdot.data';
$storeProviderFile = new StorageProviderSerialized($fileName);

$dbHost = 'localhost';
$dbUser = 'user';
$dbPass = $argv[1];
$dbName = 'eventory';
$storeProviderDB = new StorageProviderMySql($dbHost, $dbUser, $dbPass, $dbName);

for ($i = 0; $i < 10000; $i++){
	$event = $storeProviderFile->loadEventById($i);
	if (!$event instanceof Event){
		continue;
	}

	$newEvent = $storeProviderDB->createEvent($event->eventUrl, $event->eventKey);
	$newEvent->description = $event->description;
	$newEvent->created = $event->getCreated();
	$newEvent->updated = $event->getUpdated();
	$storeProviderDB->saveEvents(array($newEvent));


	$storeProviderDB->addAssetsToEvent($newEvent, $event->getAssets());
	foreach ($event->getSubUrls() as $subUrl){
	    $storeProviderDB->addSubUrlsToEvent($newEvent, $subUrl);
	}
}

// performers
foreach ($storeProviderFile->loadAllPerformers() as $performer){
    $newPerf = $storeProviderDB->createPerformer($performer->getName());
    $newPerf->setImageUrl($performer->getImageUrl());
    $newPerf->setHighlight($performer->isHighlighted());
    foreach ($performer->getSiteUrls() as $url){
        $newPerf->addSiteUrl($url);
    }
    $storeProviderDb->savePerformer($newPerf);

    foreach ($performer->getEventIds() as $eventId){
        $storeProviderDb->addPerformerToEvent($newPerformer, $eventId);
    }
}