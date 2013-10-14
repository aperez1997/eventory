<?php

require_once __DIR__ . '/../bootstrap.php';

if ($argc < 2){
	printf("Usage: %s <performerId>\n", __FILE__);
	exit();
}

$store = getStoreProvider();

$pId = $argv[1];

$rv = $store->deletePerformer($pId);

if ($rv){
	echo "success\n";
} else {
	echo "failed\n";
}


