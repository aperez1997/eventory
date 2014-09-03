<?php

namespace Eventory\Storage\File;

use Eventory\Objects\Event\Event;
use Eventory\Objects\Event\Assets\EventAsset;
use Eventory\Objects\Performers\Performer;
use Eventory\Storage\iStorageProvider;
use Eventory\Storage\StorageProviderAbstract;

class StorageProviderSerialized extends StorageProviderAbstract implements iStorageProvider
{
	const CURRENT_VERSION = 1;

	const KEY_VERSION		= 'v';
	const KEY_EVENTS		= 'e';
	const KEY_PERFORMERS	= 'p';

	protected $fileName;
	protected $loaded;
    protected $dirty;

	protected $events;
	protected $performers;

	protected $keyToIdxMap;

	public function __construct($fileName)
	{
		$this->fileName		= $fileName;
		$this->events		= array();
		$this->performers	= array();
		$this->performersHigh	= array();
		$this->keyToIdxMap	= array();
		$this->loaded		= false;
		$this->dirty        = false;
	}

	/**
	 * @param $url
	 * @param $key
	 * @return Event
	 */
	public function createEvent($url, $key)
	{
		$lookup = $this->loadEventByKey($key);
		if ($lookup instanceof Event){
			return $lookup;
		}

		$event = Event::CreateNew($url, $key);
		$id = end($this->events)->id + 1;
		$event->id = $id;
		$this->events[$id] = $event;
		$this->dirty = true;
		error_log(sprintf("creating event %s %s", $id, $key));
		return $event;
	}

	/**
	 * @param array $events		array of Event
	 */
	public function saveEvents(array $events)
	{
		$this->getEvents();
		$this->dirty = true;
	}

	/**
	 * @param int|Event $eventId
	 * @param array $assets         Array of EventAsset
	 */
	public function addAssetsToEvent($eventId, array $assets)
	{
		$event = $this->getEventFromId($eventId);
		$event->addAssets($assets);
		$this->dirty = true;
	}

	/**
	 * @param int|Event $eventId
	 * @param array $subUrls        Array of string
	 */
	public function addSubUrlsToEvent($eventId, array $subUrls)
	{
		$event = $this->getEventFromId($eventId);
		$event->addSubUrls($subUrls);
		$this->dirty = true;
	}

	/**
	 * @param array $ids
	 * @return array Event
	 */
	public function loadEventsById(array $ids)
	{
		$this->getEvents();
		$events = array();
		foreach ($ids as $id){
			if (!is_string($id) && !is_int($id)){
				throw new Exception(sprintf('invalid id [%s]', print_r($id,true)));
			}
			if (isset($this->events[$id])){
				$events[$id] = $this->events[$id];
			}
		}
		return $events;
	}

	/**
	 * @param string $key
	 * @return Event
	 */
	public function loadEventByKey($key)
	{
		$this->getEvents();
		$event = null;
		if (isset($this->keyToIdxMap[$key])){
			$idx = $this->keyToIdxMap[$key];
			if (isset($this->events[$idx])){
				$event = $this->events[$idx];
			}
		}
		return $event;
	}

	/**
	 * @param int|null $maxCount
	 * @param int|null $offset
	 * @return array Event
	 */
	public function loadRecentEvents($maxCount = null, $offset = null)
	{
		if (!isset($maxCount)){
			$maxCount = 50;
		}
		if (!isset($offset)){
			$offset = 0;
		}
		$events = array();
		foreach ($this->getEvents() as $event){
			/** @var Event $event */
			$sortKey = $event->getSortKey();
			$high = false;
			foreach ($event->getPerformerIds() as $id => $name){
				if (isset($this->performersHigh[$id])) $high = true;
			}
			if ($high) $sortKey += 3600;
			$events[strval($sortKey . $event->getId())] = $event;
		}
		ksort($events);
		$events = array_reverse($events);
		$events = array_slice($events, $offset, $maxCount);
		return $events;
	}

	/**
	 * @param string $name
	 * @return Performer
	 */
	public function createPerformer($name)
	{
		$lookup = $this->loadPerformerByName($name);
		if ($lookup instanceof Performer){
			return $lookup;
		}
		$performer = Performer::CreateNew($name);
		$id = count($this->performers) + 1;
		$performer->id = $id;
		$this->performers[$id] = $performer;
		$this->dirty = true;
		return $performer;
	}

	public function savePerformers(array $performers)
	{
		$this->getPerformers();
		foreach ($performers as $performer){
			/** @var Performer $performer */
			$id = $performer->getId();
			$this->performers[$id] = $performer;
		}
		$this->dirty = true;
	}

	/**
	 * @param array $ids
	 * @return array Performer
	 */
	public function loadPerformersByIds(array $ids)
	{
		$performers = $this->getPerformers();
		return array_intersect_key($performers, array_flip(array_values($ids)));
	}

	/**
	 * @param string $name
	 * @return Performer
	 */
	public function loadPerformerByName($name)
	{
		foreach ($this->getPerformers() as $performer){
			/** @var Performer $performer */
			if (strcasecmp($performer->getName(), $name) == 0){
				return $performer;
			}
		}
		return null;
	}

	/**
	 * @return array of Performer
	 */
	public function loadAllPerformers()
	{
		return $this->getPerformers();
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function deletePerformer($id)
	{
		$performer = $this->loadPerformerById($id);
		if (!$performer instanceof Performer){
			return false;
		}

		$performer->setDeleted();

		$events = $this->loadEventsById($performer->getEventIds());
		foreach ($events as $event){
			/** @var Event $event */
			$event->removePerformer($performer);
		}
		$this->dirty = true;

		return true;
	}

	public function addPerformerToEvent($performer, $event)
	{
		$performer = $this->getPerformerFromId($performer);
		$event = $this->getEventFromId($event);

		$event->addPerformer($performer);
		$performer->addEventId($event->getId());
		$this->dirty = true;
	}

	public function removePerformerFromEvent($performer, $event)
	{
		$performer = $this->getPerformerFromId($performer);
		$event = $this->getEventFromId($event);

		$event->removePerformer($performer);
		$performer->removeEvent($event);
		$this->dirty = true;
	}


	protected function getEvents()
	{
		if (!$this->loaded){
			$this->loadDataFromFile();
		}
		return $this->events;
	}

	protected function getPerformers()
	{
		if (!$this->loaded){
			$this->loadDataFromFile();
		}
		return $this->performers;
	}

	protected function loadDataFromFile()
	{
		$dataRaw = file_get_contents($this->fileName);
		if ($dataRaw !== false && !empty($dataRaw)){
			$data = unserialize($dataRaw);
			if (is_array($data)){
				$version = 0;
				if (isset($data[self::KEY_VERSION])){
					$version = $data[self::KEY_VERSION];
				}
				switch ($version){
					case 1:
						$this->readVersion1($data);
						break;
					default;
						$this->readVersion0($data);
						break;
				}
				$this->initKeyMap();
			}
		}
		$this->loaded = true;
	}

	protected function readVersion0($data)
	{
		$this->events		= $data;
		$this->performers	= array();
	}

	protected function readVersion1($data)
	{
		$events			= $data[self::KEY_EVENTS];
		foreach ($events as $k => $v){
			$this->events[$k] = $v;
		}
		$this->performers	= $data[self::KEY_PERFORMERS];
	}

	protected function fixMissingDataEvent(Event $event)
	{
		if (!is_int($event->getCreated()) || $event->getCreated() <= 0){
			$event->setCreated(strtotime('2013-10-01'));
		}
	}

	protected function initKeyMap()
	{
		foreach ($this->events as $k => $event){
			/** @var Event $event */
			if (!$event instanceof Event){
			    unset($this->events[$k]);
			    error_log("invalid event [". $k ."] [". print_r($event, true));
			    continue;
			}
			foreach ($event->assets as $ak => $asset){
				if (!$asset instanceof EventAsset){
					//error_log("invalid event asset [". $ak ."] [". print_r($asset, true));
					unset($event->assets[$ak]);
				}
			}

			$this->keyToIdxMap[$event->getKey()] = $event->getId();
			$this->fixMissingDataEvent($event);
		}
		foreach ($this->performers as $performer){
			/** @var Performer $performer */
			if ($performer->isHighlighted()){
				$this->performersHigh[$performer->getId()] = $performer;
			}
		}
	}

	protected function saveDataToFile()
	{
		$data = array(
			self::KEY_VERSION		=> self::CURRENT_VERSION,
			self::KEY_EVENTS		=> $this->events,
			self::KEY_PERFORMERS	=> $this->performers,
		);
		file_put_contents($this->fileName, serialize($data));
	}

	public function __destruct()
	{
	    if ($this->dirty){
		    $this->saveDataToFile();
		}
	}
}
