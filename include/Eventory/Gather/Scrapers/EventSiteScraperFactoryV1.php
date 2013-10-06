<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Gather\Scrapers;

use Eventory\Objects\EventScrapeItem;
use Eventory\Storage\iStorageProvider;

abstract class EventSiteScraperFactoryV1
{
	protected $store;
	protected $maxToScrape;

	public function __construct(iStorageProvider $store, $maxToScrape = null)
	{
		$this->store = $store;
		$this->maxToScrape = $maxToScrape;
	}

	/**
	 * @param EventScrapeItem $scrapeItem
	 * @return EventSiteScraperV1
	 */
	abstract public function getSiteScraperForScrapeItem(EventScrapeItem $scrapeItem);
}