<?php

use Eventory\Objects\Event\Assets\EventAsset;
use Eventory\Objects\Event\Event;

/** @var $event Event */
$eventId = $event->getId();
$eventKey = $event->getKey();
$description = $event->description;
$f = $page->getTimeFormat();
$created = date($f, $event->getCreated());
$updated = date($f, $event->getUpdated());

$assetContent = '';
foreach ($event->getAssets() as $asset){
	/** @var EventAsset $asset */
	$href = $asset->linkUrl;
	$src = $asset->imageUrl;
	$assetContent .= "<li class='thumb'><a href='{$href}' target='_blank'><img src='{$src}'></a></li>";
}
if ($assetContent){
	$assetContent = "<div><ul>{$assetContent}</ul></div>";
}

$urlContent = '';
foreach ($event->getSubUrls() as $url){
	$urlContent .= "<li><a href='{$url}' target='_blank'>{$url}</a></li>\n";
}
if ($urlContent){
	$urlContent = "<div>Links:<ul>{$urlContent}</ul></div>\n";
}

$performerContent = '';
foreach ($event->getPerformerIds() as $pId => $pName){
	$href = $page->getLinkPerformerView($pId);
	$performerContent .= "<li><a href='{$href}' target='_blank'>{$pName}</a></li>\n";
}

$linkPerformerAdd = '';
if ($page->isAdmin()){
	$hrefPerformerAdd = $page->getLinkEventPerformerAdd($eventId);
	$linkPerformerAdd = sprintf('<div class="performer-add"><a href="%s">Add Performer</a></div>', $hrefPerformerAdd);
}

if ($performerContent){
	$performerContent = "<div>Performers:<ul>{$performerContent}</ul>{$linkPerformerAdd}</div>\n";
} else {
	$performerContent = $linkPerformerAdd;
}

$output = "
<h2>{$eventKey}</h2>
<div>[#{$event->getId()}] Created: {$created}, Updated: {$updated}</div>
<p>{$description}</p>
{$assetContent}
{$urlContent}
{$performerContent}
\n";
echo $output;
