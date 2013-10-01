<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
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

	protected $ratePerSecond = 10;

	protected $maxToScrape = null;

	public function scrapeFromWeb()
	{
		$scrapeItems = $this->scrapeFromWebIntoScrapeItems();

		$maxToScrape = $this->maxToScrape;

		$events = array();
		foreach ($scrapeItems as $scrapeItem){
			/** @var EventScrapeItem $scrapeItem */
			if (isset($maxToScrape)){
				if ($maxToScrape-- <= 0) break;
			}

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
			usleep(1000000 / $this->ratePerSecond);
		}
		return $events;
	}

	/**
	 * @return array[EventScrapeItems]
	 */
	public function scrapeFromWebIntoScrapeItems()
	{
		$source = $this->getListSiteUrl();
		return $this->parseIntoEventScrapeItems($source);
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
			$scrapeItems[] = $scrapeItem;
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