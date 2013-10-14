<?php

/** @var $vars array */

if ($vars['p']){
	echo "<a href='{$vars['p']}'><strong>&laquo;PREV</strong></a> &nbsp; ";
}

$nextLink = "<a href='{$vars['n']}'><strong>NEXT&raquo;</strong></a>";
echo $nextLink;

$events = $vars['e'];
include __DIR__ . '/tmp_events_table.php';

echo $nextLink;