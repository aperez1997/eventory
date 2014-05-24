<?php

use Eventory\Objects\Event\Assets\EventAsset;

require_once __DIR__ .'/bootstrap.php';

$store = getStoreProvider();

$performer1 = $store->createPerformer('Galileo Arduino');
$performer2 = $store->createPerformer('Highlight halo');
$performer3 = $store->createPerformer('Third Person');
$performer2->setHighlight(true);


$url1 = 'http://hardware.slashdot.org/story/13/10/04/1735248/intel-launches-galileo-an-arduino-compatible-mini-computer';
$event1 = $store->createEvent($url1, 'key1');
$event1->description = "Although Intel is Chipzilla, the company can't help but extend its reach just a bit into the exciting and growing world of DIY makers and hobbyists.";
$event1->addSubUrls(array($url1));

$store->addPerformerToEvent($performer1, $event1);

$asset1 = new EventAsset();
$asset1->imageUrl = 'http://a.fsdn.com/sd/topics/intel_64.png';
$asset1->text = 'intel';
$asset1->key = 'icon';

$store->addAssetsToEvent($event1, array($asset1));

$store->saveEvents(array($event1));
$store->savePerformers(array($performer1, $performer2, $performer3));