<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

use Eventory\Storage\File\StorageProviderSerialized;

require_once __DIR__ . '/../../../../bootstrap.php';

function getStoreProvider()
{
	$fileName = __DIR__ .'/slashdot.data';
	$storeProvider = new StorageProviderSerialized($fileName);
	return $storeProvider;
}