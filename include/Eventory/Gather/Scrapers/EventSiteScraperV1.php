<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Gather\Scrapers;

use Eventory\Model\EventModel;
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

	/** @var EventModel */
	protected $eventModel;

	protected $maxToScrape = null;
	protected $ratePerSecond = 10;

	public function __construct(iStorageProvider $store)
	{
		$this->store		= $store;
		$this->eventModel	= new EventModel($store);
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
		$this->htmlDom = $this->getHtml($subUrl);
		if (!$this->htmlDom instanceof \simple_html_dom){
			error_log(printf("Failed to process source [%s] into dom", $subUrl));
			return;
		}

		// update event with new data
		$this->store->addSubUrlsToEvent($this->event, array($this->eventScrapeItem->eventUrl));
		$this->store->addAssetsToEvent($this->event, $this->parseGetAssets());

		$this->findPerformersForEvent();

		// Any extra data will be handled here
		$this->findExtraData();

        // Save any updates
        $this->store->saveEvents(array($this->event));

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

	public function setRatePerSecond($rate)
	{
		$this->ratePerSecond = $rate;
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

	protected function findPerformersForEvent()
	{
		$this->eventModel->findPerformersForEvent($this->event);
	}

	protected function findExtraData()
	{
		return null;
	}

        protected function getHtml($source)
        {
                // create curl resource
            $ch = curl_init();

            // set url
            curl_setopt($ch, CURLOPT_URL, $source);

            //return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

            // $output contains the output string
            $output = curl_exec($ch);

            // close curl resource to free up system resources
            curl_close($ch);
                return str_get_html($output);
        }
}
