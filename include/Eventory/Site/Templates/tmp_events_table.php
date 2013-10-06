<table class='events'>
<?php

use Eventory\Objects\Event\Event;

$i = 0;
$lastEventDay = null;
foreach ($events as $event){
	/** @var Event $event */
	$stripeClass = $i++ % 2 == 1 ? 'stripe' : '';

	$eventDay = date('m-d-Y', $event->getUpdated());

	if (isset($lastEventDay) && $lastEventDay != $eventDay){
		echo "<tr class='date-row'><td><h1>{$eventDay}</h1></td></tr>\n";
	}

	echo "<tr class='{$stripeClass}'><td>";
	include __DIR__ . '/tmp_event_display.php';
	echo "</td></tr>\n";

	$lastEventDay = $eventDay;
}

?>
</table>