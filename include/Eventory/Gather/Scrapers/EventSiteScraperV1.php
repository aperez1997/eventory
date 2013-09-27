<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Gather\Scrapers;

use Eventory\Objects\Event\Event;
use Eventory\Objects\EventScrapeItem;

abstract class EventSiteScraperV1
{
	/** @var EventScrapeItem */
	protected $eventScrapeItem;
	/** @var \simple_html_dom */
	protected $htmlDom;

	/**
	 * @param EventScrapeItem $item
	 * @return Event
	 */
	public function scrapeFromWeb(EventScrapeItem $item)
	{
		$this->eventScrapeItem = $item;

		$url = $this->eventScrapeItem->eventUrl;
		$event = $this->parseIntoEvent($url);
		return $event;
	}

	/**
	 * @return Event
	 */
	protected function parseIntoEvent($source)
	{
		$this->htmlDom = file_get_html($source);

		$event = new Event();
		$event->eventUrl		= $this->eventScrapeItem->eventUrl;
		$event->eventIdentifier	= $this->eventScrapeItem->eventIdentifier;
		$event->assets 			= $this->parseGetAssets();
		return $event;
	}

	/**
	 * @return array [EventAsset]
	 */
	abstract protected function parseGetAssets();
}