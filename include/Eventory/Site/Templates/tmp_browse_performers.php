<ul>
<?php

use Eventory\Objects\Performers\Performer;

foreach ($vars as $performer){
	/** @var Performer $performer */

	$name = $performer->getName();
	$href = $page->getLinkPerformerView($performer->getId());
	$count = count($performer->getEventIds());
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

?>
</ul>