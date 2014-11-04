<script src="/js/admin.js"></script>
<div ng-app="adminApp" ng-controller="eventPerformerCtr">
<?php

use Eventory\Objects\Performers\Performer;
use Eventory\Utils\ArrayUtils;

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

echo "<a href='#' ng-confirm-click='Are you sure?' ng-click='deletePerformer({$id});' class='delete-performer'>Delete</a>";

// build duplicate form
$performers = $page->getStorageProvider()->loadAllPerformers();
$performers = ArrayUtils::ReindexByMethod($performers, 'getName');
ksort($performers);
?>
	
<div class="performer-duplicate-box">
	<input type="hidden" ng-model="performer-id-dupe" value="<?=$id?>"/>
	<select ng-model="performer-id-real" min="1" required>
		<option value=''>--Pick One--</option>
		<?php
			foreach ($performers as $performer){
				/** @var Performer $performer */
				$pid = $performer->getId();
				$name = $performer->getName();
				echo "<option value='{$pid}'>{$name}</option>\n";
			}
		?>
	</select>
	<button ng-confirm-click='Are you sure?' ng-click='markDuplicatePerformer();'>Mark Duplicate</button>
</div>
<?php
$eventIds = $performer->getEventIds();
if (!empty($eventIds)){
	$events = $page->getStorageProvider()->loadEventsById($eventIds);
	$events = array_reverse($events);

	echo "Events:<br>\n";
	require __DIR__ . '/tmp_events_table.php';
} else {
	echo "No events<br>";
}
?>
</div>
