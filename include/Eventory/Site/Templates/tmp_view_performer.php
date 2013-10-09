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
$high = $performer->isHighlighted();

echo "<h1>{$name} #{$id}". ($high ? ' Highlighted!' : '') ."</h1>";

$img = $performer->getImageUrl();
if ($img){
	echo "<img src='{$img}'/><br>\n";
}

$urls = $performer->getSiteUrls();
foreach ($urls as $url){
	echo "<li><a href='{$url}' target='_blank'>{$url}</a></li>\n";
}

$events = $page->getStorageProvider()->loadEventsById($performer->getEventIds());
$events = array_reverse($events);

echo "Events:<br>\n";
require __DIR__ . '/tmp_events_table.php';
