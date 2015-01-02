<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2015 Zoosk Inc.
 */

namespace Suites\Storage;

use Eventory\Storage\MySql\StorageProviderMySql;
use Eventory\Tests\EventoryTestCase;

class StorageProviderMySqlTest extends EventoryTestCase
{
	public function testConstruct()
	{
		$provider = new StorageProviderMySql('', '', '', '');
		$this->assertTrue($provider instanceof StorageProviderMySql);
	}
}