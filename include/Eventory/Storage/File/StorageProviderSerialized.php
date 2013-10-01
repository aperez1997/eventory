<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Storage\File;

use Eventory\Objects\Event\Event;
use Eventory\Storage\iStorageProvider;

class StorageProviderSerialized implements iStorageProvider
{
	protected $fileName;

	protected $events;

	protected $keyToIdxMap;

	public function __construct($fileName)
	{
		$this->fileName		= $fileName;
		$this->events		= null;
		$this->keyToIdxMap	= null;
	}

	/**
	 * @param array $events		array of Event
	 */
	public function saveEvents(array $events)
	{
		$this->loadEvents();
		foreach ($events as $event){
			/** @var Event $event */
			$id = $event->getId();
			if ($id === null){
				$id = count($this->events) + 1;
				$event->id = $id;
			}
			$this->events[$id] = $event;
		}
		$this->saveEventsToFile();
	}

	/**
	 * @param array $ids
	 * @return array Event
	 */
	public function loadEventsById(array $ids)
	{
		$this->loadEvents();
		$events = array();
		// TODO
		return $events;
	}

	/**
	 * @param string $key
	 * @return Event
	 */
	public function loadEventByKey($key)
	{
		$this->loadEvents();
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
	 * @param $performerId
	 * @return array Event
	 */
	public function loadEventsByPerformer($performerId)
	{
		$this->loadEvents();
		return array();
	}

	/**
	 * @param int|null $maxCount
	 * @param int|null $offset
	 * @return array Event
	 */
	public function loadRecentEvents($maxCount = null, $offset = null)
	{
		if (empty($maxCount)){
			$maxCount = 50;
		}
		if (empty($offset)){
			$offset = 0;
		}
		$events = $this->loadEvents();
		return array_slice($events, $offset * -1, $maxCount);
	}

	protected function loadEvents()
	{
		if ($this->events === null){
			$events = array();
			printf("reading from file [%s]\n", $this->fileName);
			$eventsRaw = file_get_contents($this->fileName);
			if ($eventsRaw !== false && !empty($eventsRaw)){
				$events = unserialize($eventsRaw);
			}
			$this->events = $events;
		}
		foreach ($this->events as $event){
			/** @var Event $event */
			$this->keyToIdxMap[$event->getKey()] = $event->getId();
		}
		return $this->events;
	}

	protected function saveEventsToFile()
	{
		printf("writing to file [%s]\n", $this->fileName);
		file_put_contents($this->fileName, serialize($this->events));
	}
}