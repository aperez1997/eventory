<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

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
			default:
				return new SlashdotEventSiteScraper($this->store);
				break;
		}
		error_log(printf("unmatched domain [%s] for %s\n", $domain, $scrapeItem));
		return null;
	}
}