<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

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

	protected $maxToScrape = null;
	protected $ratePerSecond = 10;

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
		printf("scraping [%s] Left[%s]\n", $this->eventScrapeItem->eventUrl, $this->maxToScrape);
		$this->parseIntoEvent();
		return $this->event;
	}

	protected function parseIntoEvent()
	{
		$subUrl = $this->eventScrapeItem->eventUrl;

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
			$this->event = $this->store->createEvent($this->eventScrapeItem->eventUrl, $this->eventScrapeItem->eventKey);
		}

		if (isset($this->maxToScrape)) $this->maxToScrape--;
		$this->htmlDom = file_get_html($subUrl);

		// update event with new data
		$this->event->addSubUrls(array($this->eventScrapeItem->eventUrl));
		$this->event->addAssets($this->parseGetAssets());

		usleep(1000000 / $this->ratePerSecond);

		return;
	}

	protected function findEventByKey()
	{
		return $this->store->loadEventByKey($this->eventScrapeItem->eventKey);
	}

	public function setMaxToScrape($num)
	{
		$this->maxToScrape = $num;
	}

	public function doneScraping()
	{
		if (!isset($this->maxToScrape)){
			return false;
		}
		return $this->maxToScrape <= 0;
	}

	/**
	 * @return array [EventAsset]
	 */
	abstract protected function parseGetAssets();
}
