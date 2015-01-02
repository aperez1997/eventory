<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Tests;

use Eventory\Storage\File\StorageProviderSerialized;
use Eventory\Storage\iStorageProvider;

class EventoryTestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * @return iStorageProvider
	 */
	public function getStorageProvider()
	{
		return new StorageProviderSerialized('./include/Eventory/Examples/Slashdot/slashdot.data');
	}
}