<?php

require_once __DIR__ . '/../bootstrap.php';

if ($argc < 1){
	exit("not enough args\n");
}

$store = getStoreProvider();

$pId = $argv[1];

$lookup = $store->loadPerformerById($pId);
if (!isset($lookup)){
	exit(sprintf("Not found [%s]\n", $pId));
}

print "TODO\n";
