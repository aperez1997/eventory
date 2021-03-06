<?php

namespace Eventory\Examples\Slashdot;

use Eventory\Gather\Scrapers\EventListSiteScraperV1;
use Eventory\Utils\HttpUtils;

/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */
class SlashdotListSiteScraper extends EventListSiteScraperV1
{
	protected $maxToScrape = 2;

	protected function getListSiteUrl()
	{
		return 'https://slashdot.org/';
	}

	public function isNodeEventLink(\simple_html_dom_node $htmlNode)
	{
		$href = $htmlNode->href;
		$pattern = '/'.preg_quote('slashdot.org/story/', '/').'/';
		$val = preg_match($pattern, $href);
		return $val;
	}

	protected function getEventHrefFromNode(\simple_html_dom_node $htmlNode)
	{
		$href = $htmlNode->href;

		$pattern = '/' . preg_quote('//', '/'). '(.*?)\?/';
		preg_match($pattern, $href, $matches);
		return 'http://' . $matches[1];
	}

	public function getEventIdFromNode(\simple_html_dom_node $htmlNode)
	{
		return $this->getEventHrefFromNode($htmlNode);
	}
}