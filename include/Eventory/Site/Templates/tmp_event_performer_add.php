<?php

use Eventory\Objects\Event\Event;
use Eventory\Objects\Performers\Performer;
use Eventory\Site\Constants\SitePageParams;
use Eventory\Site\Constants\SitePageType;
use Eventory\Utils\ArrayUtils;

/** @var Event $event */
$event = $vars;

if (!$event instanceof Event){
	printf("Event not found!");
	return;
}

$performerContent = '';
foreach ($event->getPerformerIds() as $pId => $pName){
        $href = $page->getLinkPerformerView($pId);
        $performerContent .= "<li><a href='{$href}' target='_blank'>{$pName}</a></li>\n";
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

$postResult = '';
if ($page->hadPost()){
	$true = $page->wasPostSuccess() ? 'Success' : 'Fail';
	$postResult = sprintf("<div class='resultBox %s'>%s</div>", $true, $page->getPostResultMsg());
}

echo "
	{$postResult}
	<h1>{$eventKey}</h1>
	<div>[#{$event->getId()}] Created: {$created}, Updated: {$updated}</div>
	<p>{$description}</p>
	{$assetContent}
	{$urlContent}
	{$performerContent}";
?>
<script src="/js/admin.js"></script>
<div ng-app="adminApp" ng-controller="eventPerformerCtr">
<form xmlns="http://www.w3.org/1999/html" method="POST">
<input type="hidden" name="<?php echo SitePageParams::EVENT_ID?>" value="<?php echo $eventId?>"?>
<input type="hidden" name="<?php echo SitePageParams::PAGE?>" value="<?php echo SitePageType::EVENT_PERFORMER_ADD?>"?>
<div>
	<input id="ptype-pick" type="radio" name="<?php echo SitePageParams::TYPE?>" value="pick">
	<label>Pick Performer
	<select onchange="$('#ptype-pick').prop('checked', true);" name="<?php echo SitePageParams::PICK?>">
		<option value='}'>--Pick One--</option>
		<?php
			foreach ($performers as $performer){
				/** @var Performer $performer */
				$id = $performer->getId();
				if (in_array($id, $existingIds)){
					continue;
				}
				$name = $performer->getName();
				echo "<option value='{$id}'>{$name}</option> <div ng-click='removePerformer({$eventId}, {$id});'>X</div>\n";
			}
		?>
	</select>
	</label>
</div>
	OR
<div>
	<input id="ptype-enter" type="radio" name="<?php echo SitePageParams::TYPE?>" value="enter">
	<label>Enter Name<input type="text" name="<?php echo SitePageParams::TEXT?>" onkeydown="$('#ptype-enter').prop('checked', true);"/></label>
</div>
	<input type="submit"/><input type="submit" value="Cancel" onclick="document.back();">
</form>
</div>

