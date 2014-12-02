<?php

use Eventory\Objects\Performers\Performer;
use Eventory\Site\Browse\SiteBrowsePerformers;

/** @var array $vars */

$performers = $vars[0];
$links = $vars[1];
$sort = $vars[2];

echo '<ul class="nav"><li>Sort By:</li>';
foreach ($links as $linkArr){
	list($text, $url) = $linkArr;
	if ($url){
		$text = "<a href=\"{$url}\">{$text}</a>";
	}
	echo "<li>{$text}</li>\n";
}
echo "</ul><ul>";

if ($sort == SiteBrowsePerformers::SORT_ALPHA){
	$letters=array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
	$letterContent = array();
	foreach ($letters as $letter){
		$letter = strtoupper($letter);
		$letterContent[] = "<a href='#{$letter}>{$letter}</a>";
	}
	$letterContent = join(' ', $letterContent);
	echo "<div>{$letterContent}</div>";
}

$lastLetter = null;
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
	
	$currentLetter = strtoupper(substr($name, 0, 1));
	if ($sort == SiteBrowsePerformers::SORT_ALPHA && $lastLetter != $currentLetter){
		echo "<li><a name='{$currentLetter}'></a>-{$currentLetter}-</li>\n";
		$lastLetter = $currentLetter;
	}

	echo "
<li {$high}>
<a href='{$href}'>{$name}</a> [{$count} events] Updated: {$time}
</li>\n";
}

echo "</ul>";