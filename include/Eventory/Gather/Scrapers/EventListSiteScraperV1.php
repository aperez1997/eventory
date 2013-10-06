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

	protected $maxToScrape = null;

	public function scrapeFromWeb()
	{
		$scrapeItems = $this->scrapeFromWebIntoScrapeItems();

		$maxToScrape = $this->maxToScrape;

		$events = array();
		foreach ($scrapeItems as $scrapeItem){
			/** @var EventScrapeItem $scrapeItem */
			$eventSiteScraper = $this->siteScraperFactory->getSiteScraperForScrapeItem($scrapeItem);
			if (!$eventSiteScraper instanceof EventSiteScraperV1){
				printf("item [%s] does not map to a site scraper\n", print_r($scrapeItem, true));
				continue;
			}
			$key = $scrapeItem->eventKey;
			$existingEvent = null;
			if (isset($events[$key])){
				$existingEvent = $events[$key];
			}
			$event = $eventSiteScraper->scrapeFromWeb($scrapeItem, $existingEvent);
			$events[$event->getKey()] = $event;

			if (isset($maxToScrape)){
				if (--$maxToScrape <= 0) break;
			}
		}
		return $events;
	}

	/**
	 * @return array[EventScrapeItems]
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
	 * @return array[EventScrapeItems]
	 */
	protected function parseIntoEventScrapeItems($source)
	{
		$scrapeItems = array();

		$html = file_get_html($source);

		foreach ($html->find('a') as $htmlNode){
			/** @var \simple_html_dom_node $htmlNode */
			if (!$this->isNodeEventLink($htmlNode)){
				continue;
			}
			$href = $this->getEventHrefFromNode($htmlNode);
			$id = $this->getEventIdFromNode($htmlNode);
			$scrapeItem = $this->createEventScrapeItem($href, $id);
			$scrapeItems[$href] = $scrapeItem;
		}
		return $scrapeItems;
	}

	abstract protected function getListSiteUrl();

	abstract protected function isNodeEventLink(\simple_html_dom_node $htmlNode);

	protected function getEventHrefFromNode(\simple_html_dom_node $htmlNode)
	{
		return $htmlNode->href;
	}

	abstract protected function getEventIdFromNode(\simple_html_dom_node $htmlNode);

	protected function createEventScrapeItem($url, $id)
	{
		$scrapeItem = new EventScrapeItem();
		$scrapeItem->eventUrl = $url;
		$scrapeItem->eventKey = $id;
		return $scrapeItem;
	}
}