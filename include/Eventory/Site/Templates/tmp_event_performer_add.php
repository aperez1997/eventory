<form xmlns="http://www.w3.org/1999/html">
<?php

use Eventory\Objects\Event\Event;
use Eventory\Objects\Performers\Performer;
use Eventory\Utils\ArrayUtils;

/** @var Event $event */
$event = $vars;

if (!$event instanceof Event){
	printf("Event not found!");
	return;
}

$existingIds = array_keys($event->getPerformerIds());

$performers = $page->getStorageProvider()->loadAllPerformers();
$performers = ArrayUtils::ReindexByMethod($performers, 'getName');
ksort($performers);


$eventId = $event->getId();
$eventKey = $event->getKey();
$description = $event->description;
$f = $page->getTimeFormat();
$created = date($f, $event->getCreated());
$updated = date($f, $event->getUpdated());

echo "
	<h1>{$eventKey}</h1>
	<div>[#{$event->getId()}] Created: {$created}, Updated: {$updated}</div>
	<p>{$description}</p>
	{$assetContent}
	{$urlContent}
	{$performerContent}";

?>
<div>
	<label>Pick Performer</label>
	<select>
		<?php
			foreach ($performers as $performer){
				/** @var Performer $performer */
				$id = $performer->getId();
				if (in_array($id, $existingIds)){
					continue;
				}
				$name = $performer->getName();
				echo "<option value='{$id}'>{$name}</option>\n";
			}
		?>
	</select>
</div>
	OR
<div>
	<label>Enter Name</label><input type="text"/>
</div>
	<input type="submit"/><input type="submit" value="Cancel">
</form>

