<?php

require_once __DIR__ . '/../bootstrap.php';

use Eventory\Objects\Event\Event;
use Eventory\Objects\Performers\Performer;
use Eventory\Storage\File\StorageProviderSerialized;
use Eventory\Storage\MySql\StorageProviderMySql;


$fileName = __DIR__ .'/slashdot.data';
$storeProviderFile = new StorageProviderSerialized($fileName);

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = 'tempDawg1crizal';
$dbName = 'eventory';
$storeProviderDB = new StorageProviderMySql($dbHost, $dbUser, $dbPass, $dbName);

try {

for ($i = 0; $i < 10000; $i++){
	$event = $storeProviderFile->loadEventById($i);
	if (!$event instanceof Event){
		continue;
	}

	$newEvent = $storeProviderDB->createEvent($event->eventUrl, $event->eventKey);
	$newEvent->description = $event->description;
	$newEvent->setCreated($event->getCreated());
	$newEvent->setUpdated($event->getUpdated());
	$storeProviderDB->saveEvents(array($newEvent));


	$storeProviderDB->addAssetsToEvent($newEvent, $event->getAssets());
	$storeProviderDB->addSubUrlsToEvent($newEvent, $event->getSubUrls());
}

// performers
foreach ($storeProviderFile->loadAllPerformers() as $performer){
	/** @var Performer $performer*/
	/** @var Performer $newPerf */
	$newPerf = $storeProviderDB->createPerformer($performer->getName());
    $newPerf->setImageUrl($performer->getImageUrl());
    $newPerf->setHighlight($performer->isHighlighted());
    foreach ($performer->getSiteUrls() as $url){
        $newPerf->addSiteUrl($url);
    }
    $storeProviderDb->savePerformer($newPerf);

    foreach ($performer->getEventIds() as $eventId){
        $storeProviderDb->addPerformerToEvent($newPerf, $eventId);
    }
}

} catch (Exception $ex){
	printf("Exception (%s) %s\n@ %s", get_class($ex), $ex->getMessage(), $ex->getTraceAsString());
}
