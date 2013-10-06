<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Storage\File;

use Eventory\Objects\Event\Assets\EventAsset;
use Eventory\Objects\Event\Event;
use Eventory\Objects\Performers\Performer;
use Eventory\Storage\iStorageProvider;

class StorageProviderSerialized implements iStorageProvider
{
	const CURRENT_VERSION = 1;

	const KEY_VERSION		= 'v';
	const KEY_EVENTS		= 'e';
	const KEY_PERFORMERS	= 'p';

	protected $fileName;
	protected $loaded;

	protected $events;
	protected $performers;

	protected $keyToIdxMap;

	public function __construct($fileName)
	{
		$this->fileName		= $fileName;
		$this->events		= array();
		$this->performers	= array();
		$this->keyToIdxMap	= array();
		$this->loaded		= false;
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
		$id = count($this->events) + 1;
		$event->id = $id;
		$this->events[$id] = $event;
		return $event;
	}

	/**
	 * @param array $events		array of Event
	 */
	public function saveEvents(array $events)
	{
		$this->getEvents();
		foreach ($events as $event){
			/** @var Event $event */
			$id = $event->getId();
			$this->events[$id] = $event;
		}
		// will also save performers!
		$this->saveDataToFile();
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
		$this->saveDataToFile();
	}

	/**
	 * @param $performerId
	 * @return Performer
	 */
	public function loadPerformerById($performerId)
	{
		$performers = $this->getPerformers();
		if (isset($performers[$performerId])){
			return $performers[$performerId];
		}
		return null;
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
		$events = $this->getEvents();
		$events = array_reverse($events);
		$events = array_slice($events, $offset, $maxCount);
		return $events;
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
			}
		}
		$this->loaded = true;
	}

	protected function readVersion0($data)
	{
		$this->events		= $data;
		$this->performers	= array();
		$this->initKeyMap();
	}

	protected function readVersion1($data)
	{
		$this->events		= $data[self::KEY_EVENTS];
		$this->performers	= $data[self::KEY_PERFORMERS];
		$this->initKeyMap();
	}

	protected function initKeyMap()
	{
		foreach ($this->events as $event){
			/** @var Event $event */
			$this->keyToIdxMap[$event->getKey()] = $event->getId();
		}
	}

	protected function saveDataToFile()
	{
		printf("writing to file [%s]\n", $this->fileName);
		$data = array(
			self::KEY_VERSION		=> self::CURRENT_VERSION,
			self::KEY_EVENTS		=> $this->events,
			self::KEY_PERFORMERS	=> $this->performers,
		);
		file_put_contents($this->fileName, serialize($data));
	}
}
