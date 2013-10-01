<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Examples\Slashdot;

use Eventory\Gather\Scrapers\EventSiteScraperV1;
use Eventory\Objects\Event\Assets\EventAsset;

class SlashdotEventSiteScraper extends EventSiteScraperV1
{
	function parseGetAssets()
	{
		$assets = array();

		/** @var \simple_html_dom_node $topic */
		$topic = $this->htmlDom->find('span.topic', 0);
		if ($topic){
			$asset = new EventAsset();
			$a = $topic->find('a', 0);
			$asset->linkUrl = $a->href;
			$asset->imageUrl = $a->find('img', 0)->src;
			$asset->key = 'icon';
			$assets[] = $asset;
		}

		return $assets;
	}
}