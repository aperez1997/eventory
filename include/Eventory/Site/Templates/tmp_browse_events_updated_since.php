<?php

/** @var $vars array */


if (isset($vars['n'])){
	$nextLink = "<a href='{$vars['n']}'><strong>NEXT&raquo;</strong></a>";
	echo $nextLink;
}


?>
<h1>Events updated since <?=$vars['u']?></h1>
<?php

$events = $vars['e'];
include __DIR__ . '/tmp_events_table.php';


if (isset($nextLink)){
	echo $nextLink;
}