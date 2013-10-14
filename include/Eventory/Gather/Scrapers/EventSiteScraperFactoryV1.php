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
	protected $ratePerSecond;
	protected $siteScrapers;

	public function __construct(iStorageProvider $store, $maxToScrape = null, $ratePerSecond = null)
	{
		if (isset($maxToScrape)){
			$maxToScrape = intval($maxToScrape);
		}
		$this->store = $store;
		$this->maxToScrape = $maxToScrape;
		$this->ratePerSecond = $ratePerSecond;
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
			/** @var EventSiteScraperV1 $scraper */
			$scraper = new $class($this->store);
			$this->siteScrapers[$class] = $scraper;
			if ($this->maxToScrape){
				$scraper->setMaxToScrape($this->maxToScrape);
			}
			if (isset($this->ratePerSecond)){
				$scraper->setRatePerSecond($this->ratePerSecond);
			}
		}
		return $this->siteScrapers[$class];
	}
}
