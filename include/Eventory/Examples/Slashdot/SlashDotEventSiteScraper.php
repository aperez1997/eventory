<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Examples\Slashdot;

use Eventory\Gather\Scrapers\EventSiteScraperV1;
use Eventory\Objects\Event\Assets\EventAsset;
use Eventory\Utils\HttpUtils;

class SlashdotEventSiteScraper extends EventSiteScraperV1
{
	function parseGetAssets()
	{
		$assets = array();

		$baseUrl = HttpUtils::GetDomainFromUrl($this->eventScrapeItem->eventUrl);

		/** @var \simple_html_dom_node $topic */
		$topic = $this->htmlDom->find('span.topic', 0);
		if ($topic){
			$asset = new EventAsset();
			$a = $topic->find('a', 0);
			$asset->linkUrl = $a->href;
			$asset->imageUrl = 'http:' . $a->find('img', 0)->src;
			$asset->key = 'icon';
			$assets[] = $asset;
		}

		return $assets;
	}
}