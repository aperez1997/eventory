<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Gather\Scrapers;

use Eventory\Objects\EventScrapeItem;
use Eventory\Storage\iStorageProvider;

abstract class EventSiteScraperFactoryV1
{
	protected $store;

	public function __construct(iStorageProvider $store)
	{
		$this->store = $store;
	}

	/**
	 * @param EventScrapeItem $scrapeItem
	 * @return EventSiteScraperV1
	 */
	abstract public function getSiteScraperForScrapeItem(EventScrapeItem $scrapeItem);
}