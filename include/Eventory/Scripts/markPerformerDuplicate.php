<?php

use Eventory\Model\EventModel;

require_once __DIR__ . '/../bootstrap.php';

if ($argc < 3){
	printf("Usage: %s <dupePerformerId> <realPerformerId>\n", __FILE__);
	exit();
}

$store = getStoreProvider();
$model = new EventModel($store);

$dupeId = $argv[1];
$realId = $argv[2];

$model->markPerformerIdDuplicate($dupeId, $realId);