<table class='thumbs'>
<?php
use Eventory\Objects\Event\Assets\EventAsset;
use Eventory\Objects\Event\Event;

global $events;

$i = 0;
foreach ($events as $event){
	/** @var Event $event */
	$eventKey = $event->getKey();
	$description = $event->description;

	$assetContent = '';
	foreach ($event->getAssets() as $asset){
		/** @var EventAsset $asset */
		$href = $asset->linkUrl;
		$src = $asset->imageUrl;
		$assetContent .= "<li class='thumb'><a href='{$href}' target='_blank'><img src='{$src}'</a></li>";
	}

	$stripeClass = $i++ % 2 == 0 ? 'stripe' : '';

	$urlContent = '';
	foreach ($event->getSubUrls() as $url){
		$urlContent .= "<li><a href='{$url}'>{$url}</a></li>";
	}

	$output = "
<tr class='{$stripeClass}'>
	<h1>{$eventKey}</h1> #{$event->getId()}
	<p>{$description}</p>
	<div><ul>{$assetContent}</ul></div>
	<div><ul>{$urlContent}</ul></div>
</tr>\n";
	echo $output;
}

?>
</table>