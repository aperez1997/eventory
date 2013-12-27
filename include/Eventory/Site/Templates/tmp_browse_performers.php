<?php

use Eventory\Objects\Performers\Performer;

/** @var array $vars */

$performers = $vars[0];
$links = $vars[1];

echo '<ul class="nav"><li>Sort By:</li>';
foreach ($links as $linkArr){
	list($text, $url) = $linkArr;
	if ($url){
		$text = "<a href=\"{$url}\">{$text}</a>";
	}
	echo "<li>{$text}</li>\n";
}
echo "</ul><ul>";

foreach ($performers as $performer){
	/** @var Performer $performer */

	$name = $performer->getName();
	$href = $page->getLinkPerformerView($performer->getId());
	$count = $performer->getEventCount();
	$time = date('m-d-Y h:i:sA', $performer->getUpdated());

	$high = '';
	if ($performer->isHighlighted()){
		$high = 'class="highlight"';
	}

	echo "
<li {$high}>
<a href='{$href}'>{$name}</a> [{$count} events] Updated: {$time}
</li>\n";
}

echo "</ul>";