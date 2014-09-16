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
$assetClass = 'thumb';
if (count($event->getAssets()) > 15){
	$assetClass .= ' tiny';
} else if (count($event->getAssets()) > 10){
	$assetClass .= ' small';
}
foreach ($event->getAssets() as $asset){
	/** @var EventAsset $asset */
	$href = $asset->linkUrl;
	$src = $asset->imageUrl;
	$assetContent .= "<li class='{$assetClass}'><a href='{$href}' target='_blank'><img src='image.php?url={$src}'></a></li>";
}
if ($assetContent){
	$assetContent = "<div class='assets'><ul>{$assetContent}</ul></div>";
}

$urlContent = '';
foreach ($event->getSubUrls() as $url){
	$urlContent .= "<li><a href='{$url}' target='_blank'>{$url}</a></li>\n";
}
if ($urlContent){
	$urlContent = "<div class='links'>Links:<ul>{$urlContent}</ul></div>\n";
}

$performerContent = '';
foreach ($event->getPerformerIds() as $pId => $pName){
	$href = $page->getLinkPerformerView($pId);
	$bId = 'button-' . join('-', array($eventId, $pId));
	$performerContent .= "
<li><a href='{$href}' target='_blank'>{$pName}</a> 
<button data-clipboard-text='{$pName}' class='copy' id='{$bId}'>Copy</button>
<script>
var client = new ZeroClipboard($('#{$bId}'));
</script>
</li>\n";
}

$linkPerformerAdd = '';
if ($page->isAdmin()){
	$hrefPerformerAdd = $page->getLinkEventPerformerAdd($eventId);
	$linkPerformerAdd = sprintf('<a href="%s" target="_blank">Add Performer</a>', $hrefPerformerAdd);
}

if ($performerContent){
	$performerContent = "<div class='performers'>Performers: {$linkPerformerAdd}<ul>{$performerContent}</ul></div>\n";
} else {
	$performerContent = $linkPerformerAdd;
}

if (count($event->getAssets()) == 1){
echo "<div class='mono'>
  <div class='left'>
    <h2 class='key'>{$eventKey}</h2>
    <div class='info'>[#{$event->getId()}] Created: {$created}, Updated: {$updated}</div>
    <div class='description'><p>{$description}</p></div>
    {$urlContent}
    {$performerContent}
  </div>
  {$assetContent}
  </div>";
} else {
  echo "
<div class='regular'>
<h2 class='key'>{$eventKey}</h2>
<div class='info'>[#{$event->getId()}] Created: {$created}, Updated: {$updated}</div>
<div class='description'><p>{$description}</p></div>
{$assetContent}
{$urlContent}
{$performerContent}
</div>
\n";
}
