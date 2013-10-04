<?php

namespace Eventory\Examples\Slashdot;

use Eventory\Gather\Scrapers\EventListSiteScraperV1;
use Eventory\Utils\HttpUtils;

/**
 * @author Tony Perez <aperez1997@yahoo.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */
class SlashdotListSiteScraper extends EventListSiteScraperV1
{
	protected $maxToScrape = 6;

	protected function getListSiteUrl()
	{
		return 'http://slashdot.org';
	}

	public function isNodeEventLink(\simple_html_dom_node $htmlNode)
	{
		$href = $htmlNode->href;
		$pattern = '/'.preg_quote('news.slashdot.org/story/', '/').'/';
		return preg_match($pattern, $href);
	}

	protected function getEventHrefFromNode(\simple_html_dom_node $htmlNode)
	{
		return 'http:'.$htmlNode->href;
	}

	public function getEventIdFromNode(\simple_html_dom_node $htmlNode)
	{
		return $this->getEventHrefFromNode($htmlNode);
	}
}