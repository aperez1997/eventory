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
echo "<label>Highlight?</label><input type=checkbox ng-click='toggleHighlight({$id});' " . ($high ? "Checked" : "") . " />";

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
$performers = $page->getStorageProvider()->loadActivePerformerNames();
asort($performers);
?>
	
<div class="performer-duplicate-box">
	<select ng-model="performerIdReal" min="1" required>
		<option value=''>--Pick One--</option>
		<?php
			foreach ($performers as $pid => $name){
				if ($pid == $id){ 
					continue; // don't include current performer
				}
				echo "<option value='{$pid}'>{$name}</option>\n";
			}
		?>
	</select>
	<button ng-confirm-click='Are you sure?' ng-click='markDuplicate(<?=$id?>);'>Mark Duplicate</button>
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
