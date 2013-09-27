<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Gather\Scrapers;

use Eventory\Objects\EventScrapeItem;

abstract class EventSiteScraperFactoryV1
{
	/**
	 * @param EventScrapeItem $scrapeItem
	 * @return EventSiteScraperV1
	 */
	abstract public function getSiteScraperForScrapeItem(EventScrapeItem $scrapeItem);
}