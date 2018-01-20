<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2018 Zoosk Inc.
 */

namespace Eventory\Storage\InMemory;

use Eventory\Storage\File\StorageProviderSerialized;
use Eventory\Storage\iStorageProvider;

/**
 * Just stores everything in memory, so we can test scrapers
 */
class InMemoryStorageProvider extends StorageProviderSerialized implements iStorageProvider
{
	public function __construct()
	{
		parent::__construct('');
	}
	
	protected function loadDataFromFile()
	{
		// there is no file
		return array();
	}
	
	protected function saveDataToFile()
	{
		// no-op. there is no file
	}
}