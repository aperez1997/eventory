<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Examples;

use Eventory\Examples\Slashdot\SlashdotEventSiteScraper;
use Eventory\Gather\Scrapers\EventSiteScraperFactoryV1;
use Eventory\Objects\EventScrapeItem;
use Eventory\Utils\HttpUtils;

class ExampleSiteScraperFactory extends EventSiteScraperFactoryV1
{
	public function getSiteScraperForScrapeItem(EventScrapeItem $scrapeItem)
	{
		$domain = HttpUtils::GetDomainFromUrl($scrapeItem->eventUrl);
		switch ($domain){
			case 'news.slashdot.org':
				return new SlashdotEventSiteScraper($this->store);
				break;
		}
		printf("unmatched domain [%s]\n", $domain);
		return null;
	}
}