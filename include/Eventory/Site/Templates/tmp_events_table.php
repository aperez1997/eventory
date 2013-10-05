<table class='events'>
<?php

use Eventory\Objects\Event\Event;

$i = 0;
foreach ($events as $event){
	/** @var Event $event */
	$stripeClass = $i++ % 2 == 1 ? 'stripe' : '';

	echo "<tr class='{$stripeClass}'><td>";
	include __DIR__ . '/tmp_event_display.php';
	echo "</td></tr>\n";
}

?>
</table>