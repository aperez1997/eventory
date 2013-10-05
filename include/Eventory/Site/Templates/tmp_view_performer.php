<?php

use Eventory\Objects\Performers\Performer;

$performer = $vars;

if (!$performer instanceof Performer){
	echo "Not Found\n";
	return;
}

/** @var Performer $performer */
$name = $performer->getName();
$id = $performer->getId();
echo "<h1>{$name} #{$id}</h1>";

$events = $page->getStorageProvider()->loadEventsById($performer->getEventIds());

echo "Events:<br>\n";
require __DIR__ . '/tmp_events_table.php';