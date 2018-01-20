<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Gather\Scrapers;

use Eventory\Objects\EventScrapeItem;

abstract class EventListSiteScraperV1
{
	/** @var EventSiteScraperFactoryV1 */
	protected $siteScraperFactory;

	public function __construct(EventSiteScraperFactoryV1 $siteScraperFactory)
	{
		$this->siteScraperFactory = $siteScraperFactory;
	}

	protected $content;

	/**
	 * @return array
	 */
	public function scrapeFromWeb()
	{
		$scrapeItems = $this->scrapeFromWebIntoScrapeItems();

		$events = array();
		foreach ($scrapeItems as $scrapeItem){
			/** @var EventScrapeItem $scrapeItem */
			$eventSiteScraper = $this->siteScraperFactory->getSiteScraperForScrapeItem($scrapeItem);
			if (!$eventSiteScraper instanceof EventSiteScraperV1){
				error_log(printf("item [%s] does not map to a site scraper\n", $scrapeItem));
				continue;
			}
			$key = $scrapeItem->eventKey;
			$existingEvent = null;
			if (isset($events[$key])){
				$existingEvent = $events[$key];
			}
			$event = $eventSiteScraper->scrapeFromWeb($scrapeItem, $existingEvent);
			$events[$event->getKey()] = $event;

			if ($eventSiteScraper->doneScraping()){
				break;
			}
		}
		return array();
	}

	/**
	 * @return EventScrapeItem[]
	 */
	public function scrapeFromWebIntoScrapeItems()
	{
		$sources = $this->getListSiteUrl();
		if (!is_array($sources)){
			$sources = array($sources);
		}

		$scrapeItems = array();
		foreach ($sources as $source){
			$newItems = $this->parseIntoEventScrapeItems($source);
			$scrapeItems = array_merge($scrapeItems, $newItems);
		}

		return $scrapeItems;
	}

	/**
	 * @param string $source - filename or url
	 * @return EventScrapeItem[]
	 */
	public function parseIntoEventScrapeItems($source)
	{
		$scrapeItems = array();

		$html = $this->getHtml($source);
		if (!$html instanceof \simple_html_dom){
			error_log(printf("Failed to get html for source [%s]", $source));
			return array();
		}

		foreach ($html->find('a') as $htmlNode){
			/** @var \simple_html_dom_node $htmlNode */
			if (!$this->isNodeEventLink($htmlNode)){
				continue;
			}
			$href = $this->getEventHrefFromNode($htmlNode);
			$id = $this->getEventIdFromNode($htmlNode);
			$scrapeItem = $this->createEventScrapeItem($href, $id);
			$scrapeItem->eventThumb = $this->getEventThumbFromNode($htmlNode);
			$this->addExtraToScrapeItem($htmlNode, $scrapeItem);
			$scrapeItems[$href] = $scrapeItem;
		}
		return $scrapeItems;
	}

	/**
	 * @param string $source - file or url
	 * @return bool|\simple_html_dom
	 */
	protected function getHtml($source)
	{
		if (is_file($source)){
			return file_get_html($source);
		}

		// create curl resource
	    $ch = curl_init();

	    // set url
	    curl_setopt($ch, CURLOPT_URL, $source);

	    //return the transfer as a string
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

	    // $output contains the output string
	    $output = curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$errorMessage = curl_error($ch);

	    // close curl resource to free up system resources
	    curl_close($ch);
	    
		if ($responseCode != 200){
			error_log(printf("Failed to curl source %s with [%s:%s]", $source, $responseCode, $errorMessage));
			return false;
		}
	    
		return str_get_html($output);
	}

	abstract protected function getListSiteUrl();

	abstract protected function isNodeEventLink(\simple_html_dom_node $htmlNode);

	protected function getEventHrefFromNode(\simple_html_dom_node $htmlNode)
	{
		return $htmlNode->href;
	}

	abstract protected function getEventIdFromNode(\simple_html_dom_node $htmlNode);

	protected function getEventThumbFromNode(\simple_html_dom_node $htmlNode)
	{
		return null;
	}

	protected function createEventScrapeItem($url, $id)
	{
		$scrapeItem = new EventScrapeItem();
		$scrapeItem->eventUrl = $url;
		$scrapeItem->eventKey = $id;
		return $scrapeItem;
	}

	protected function addExtraToScrapeItem(\simple_html_dom_node $htmlNode, EventScrapeItem $scrapeItem)
	{
		// do nothing
	}
}
