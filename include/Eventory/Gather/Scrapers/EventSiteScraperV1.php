<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Gather\Scrapers;

use Eventory\Objects\Event\Event;
use Eventory\Objects\EventScrapeItem;
use Eventory\Storage\iStorageProvider;

abstract class EventSiteScraperV1
{
	/** @var EventScrapeItem */
	protected $eventScrapeItem;

	/** @var Event */
	protected $event;

	/** @var \simple_html_dom */
	protected $htmlDom;

	/** @var iStorageProvider */
	protected $store;

	public function __construct(iStorageProvider $store)
	{
		$this->store = $store;
	}

	/**
	 * @param EventScrapeItem $item
	 * @param Event|null $existingEvent
	 * @return Event
	 */
	public function scrapeFromWeb(EventScrapeItem $item, Event $existingEvent = null)
	{
		$this->eventScrapeItem	= $item;
		$this->event			= $existingEvent;
		printf("scraping [%s]\n", $this->eventScrapeItem->eventUrl);
		$this->parseIntoEvent();
		return $this->event;
	}

	protected function parseIntoEvent()
	{
		$subUrl = $this->eventScrapeItem->eventUrl;
		$this->htmlDom = file_get_html($subUrl);

		if (!$this->event instanceof Event){
			// not sent; try to find it
			$this->event = $this->findEventByKey();
		}
		if ($this->event instanceof Event){
			if (in_array($subUrl, $this->event->getSubUrls())){
				// event sub-url already processed; ignore it and return now
				printf("event id [%s] sub-url [%s] already processed\n", $this->event->getId(), $subUrl);
				return;
			}
		} else {
			// completely new event
			$this->event = new Event();
			$this->event->eventUrl		= $this->eventScrapeItem->eventUrl;
			$this->event->eventKey		= $this->eventScrapeItem->eventKey;
		}

		// update event with new data
		$this->event->addSubUrls(array($this->eventScrapeItem->eventUrl));
		$this->event->addAssets($this->parseGetAssets());
		return;
	}

	protected function findEventByKey()
	{
		return $this->store->loadEventByKey($this->eventScrapeItem->eventKey);
	}

	/**
	 * @return array [EventAsset]
	 */
	abstract protected function parseGetAssets();
}