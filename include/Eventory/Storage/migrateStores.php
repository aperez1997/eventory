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
$skipped = 0;

$mysql = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
foreach (explode(';', "delete from events; alter table events auto_increment = 1; delete from event_assets; delete from event_sub_urls; delete from performers; alter table performers auto_increment = 1; delete from event_performers") as $query){
	$mysql->query($query);
}

try {

for ($i = 1; $i < 10000; $i++){

	$event = $storeProviderFile->loadEventById($i);
	if (!$event instanceof Event){
		$skipped++;
		printf("Invalid event %s[%s]\n", $i, gettype($event));
		continue;
	}

	$eventKey = $event->eventKey;
	if (empty($eventKey)){
		$skipped++;
		printf("Event with no key %s\n", $i);
		continue;
	}

	$newEvent = $storeProviderDB->createEventWithId($i, $event->eventUrl, $eventKey);
	$newEvent->description = $event->description;
	$newEvent->setCreated($event->getCreated());
	$newEvent->setUpdated($event->getUpdated());
	$storeProviderDB->saveEvents(array($newEvent));

	$storeProviderDB->addAssetsToEvent($newEvent, $event->getAssets());
	$storeProviderDB->addSubUrlsToEvent($newEvent, $event->getSubUrls());
}

printf("Skipped %s events\n", $skipped);

// performers
$skippedLinks = 0;
foreach ($storeProviderFile->loadAllPerformers() as $performer){
	/** @var Performer $performer*/
	/** @var Performer $newPerf */
	$newPerf = $storeProviderDB->createPerformer($performer->getName());
    $newPerf->setImageUrl($performer->getImageUrl());
    $newPerf->setHighlight($performer->isHighlighted());
    foreach ($performer->getSiteUrls() as $url){
        $newPerf->addSiteUrl($url);
    }
    $storeProviderDB->savePerformer($newPerf);

	foreach ($performer->getEventIds() as $eventId){
		try {
			$storeProviderDB->addPerformerToEvent($newPerf, $eventId);
		} catch (Exception $ex){
			printf("Missing event ? %s:%s %s\n", $newPerf->getId(), $eventId, $ex->getMessage());
			$skippedLinks++;
		}
	}
}
printf("Skipped %s links\n", $skippedLinks);

} catch (Exception $ex){
	printf("Exception (%s) %s\n", get_class($ex), $ex->__toString());
}
