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
	protected $siteScrapers;

	public function __construct(iStorageProvider $store, $maxToScrape = null)
	{
		if (isset($maxToScrape)){
			$maxToScrape = intval($maxToScrape);
		}
		$this->store = $store;
		$this->maxToScrape = $maxToScrape;
		$this->siteScrapers = array();
	}

	/**
	 * @param EventScrapeItem $scrapeItem
	 * @return EventSiteScraperV1
	 */
	abstract public function getSiteScraperForScrapeItem(EventScrapeItem $scrapeItem);

	protected function createScraper($class)
	{
		if (!isset($this->siteScrapers[$class])){
			$scraper = new $class($this->store);
			$this->siteScrapers[$class] = $scraper;
			if ($this->maxToScrape){
				$scraper->setMaxToScrape($this->maxToScrape);
			}
		}
		return $this->siteScrapers[$class];
	}
}
