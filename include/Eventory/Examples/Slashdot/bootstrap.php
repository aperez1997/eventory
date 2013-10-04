<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

use Eventory\Storage\File\StorageProviderSerialized;

require_once __DIR__ . '/../../../../bootstrap.php';

function getStoreProvider()
{
	$fileName = __DIR__ .'/slashdot.data';
	$storeProvider = new StorageProviderSerialized($fileName);
	return $storeProvider;
}