<?php

namespace Eventory\Storage\MySql;

use Eventory\Objects\Event\Assets\EventAsset;
use Eventory\Objects\Event\Event;
use Eventory\Objects\Performers\Performer;
use Eventory\Storage\iStorageProvider;
use Eventory\Storage\StorageProviderAbstract;
use Eventory\Utils\ArrayUtils;

class StorageProviderMySql extends StorageProviderAbstract implements iStorageProvider
{
	protected $conn;

	protected $dbHost;
	protected $dbUser;
	protected $dbPass;
	protected $dbName;

	public function __construct($dbHost, $dbUser, $dbPass, $dbName)
	{
		$this->dbHost = $dbHost;
		$this->dbUser = $dbUser;
		$this->dbPass = $dbPass;
		$this->dbName = $dbName;
	}

	/**
	 * @param $url
	 * @param $key
	 * @return Event
	 * @throws \Exception
	 */
	public function createEvent($url, $key)
	{
		$sql = "INSERT INTO events (url, `key`, updated, created) values (?, ?, NOW(), NOW())";
		$stmt = $this->getConnection()->prepare($sql);
		$stmt->bind_param('ss', $url, $key);
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}

		$event = Event::CreateNew($url, $key);
		$event->id = $stmt->insert_id;
		return $event;
	}

	public function createEventWithId($id, $url, $key)
	{
		$sql = "INSERT INTO events (id, url, `key`, created) values (?, ?, ?, NOW())";
		$stmt = $this->getConnection()->prepare($sql);
		$stmt->bind_param('iss', $id, $url, $key);
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}

		$event = Event::CreateNew($url, $key);
		$event->id = $stmt->insert_id;
		return $event;
	}

	/**
	 * @param array $events		array of Event
	 */
	public function saveEvents(array $events)
	{
		foreach ($events as $event){
			$this->saveEvent($event);
		}
	}

	protected function saveEvent(Event $event)
	{
		$updates = array(
			'key' => $event->getKey(),
			'url' => $event->eventUrl,
			'description' => $event->getDescription(),
			// TODO: remove these lines
			'created' => gmdate("Y-m-d H:m:i",$event->getCreated()),
			'updated' => gmdate("Y-m-d H:m:i",$event->getUpdated())
		);
		$rv = $this->updateRecord('events', $event->getId(), $updates);
		return $rv;
	}

	/**
	 * Used to mark that an event was updated now
	 * @param Event $event
	 * @throws \Exception
	 */
	protected function markEventUpdated(Event $event)
	{
		$sql = "UPDATE events SET updated = NOW() WHERE id = ?";
		$stmt = $this->getConnection()->prepare($sql);
		$this->bindParams($stmt, array($event->getId()));
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}
		$event->setUpdated(time());
	}

	/**
	 * @param int|Event $eventId
	 * @param array $assets         Array of EventAsset
	 * @throws \Exception
	 */
	public function addAssetsToEvent($eventId, array $assets)
	{
		$event = $this->getEventFromId($eventId);

		$existingAssets = $event->getAssets();
		$existingAssets = ArrayUtils::ReindexByProperty($existingAssets, 'key');

		$changed = false;
		foreach ($assets as $asset){
			/** @var EventAsset $asset */
			if (!$asset instanceof EventAsset){
				throw new \Exception(sprintf("Invalid Asset %s", print_r($asset, true)));	
			}
			if (array_key_exists($asset->key, $existingAssets)){
				// skip any assets we already have
				continue;
			}
			$changed = true;

			$sql = "INSERT INTO event_assets (event_id, `key`, `type`, hostUrl, imageUrl, linkUrl, text)
					VALUES (?, ?, ?, ?, ?, ?, ?)";
			$stmt = $this->getConnection()->prepare($sql);
			$binds = array($event->getId(), $asset->key, $asset->type, $asset->hostUrl, $asset->imageUrl, $asset->linkUrl, $asset->text);
			$this->bindParams($stmt, $binds);
			if (!$stmt->execute()){
				throw new \Exception(sprintf('db failure: %s from %s w %s', $stmt->error, $sql, print_r($binds, true)));
			}
		}
		$event->addAssets($assets);
		if ($changed){
			$this->markEventUpdated($event);
		}
	}

	/**
	 * @param int|Event $eventId
	 * @param array $subUrls        Array of string
	 * @throws \Exception
	 */
	public function addSubUrlsToEvent($eventId, array $subUrls)
	{
		$event = $this->getEventFromId($eventId);

		$changed = false;
		foreach ($subUrls as $subUrl){
			if (in_array($subUrl, $event->getSubUrls())){
				// skip any subUrls we already have
				continue;
			}

			$changed = true;
			$sql = "INSERT INTO event_sub_urls (event_id, url) VALUES (?, ?)";
			$stmt = $this->getConnection()->prepare($sql);
			$this->bindParams($stmt, array($event->getId(), $subUrl));
			if (!$stmt->execute()){
				throw new \Exception(sprintf('db failure %s', $stmt->error));
			}
		}
		$event->addSubUrls($subUrls);
		if ($changed){
			$this->markEventUpdated($event);
		}
	}

	/**
	 * @param array $ids
	 * @return array Event
	 * @throws \Exception
	 */
	public function loadEventsById(array $ids)
	{
		$events = array();
		$results = $this->fetchResultsByIds('events', $ids);
		foreach ($results as $result){
			$event = Event::CreateFromData($result);
			$events[$event->getId()] = $event;
		}
		if (count($events)){
			$this->postEventLoad($events);
		}
		return $events;
	}

	/**
	 * @param string $key
	 * @return Event
	 * @throws
	 */
	public function loadEventByKey($key)
	{
		$results = $this->fetchResultsByKey('events', 'key', $key);
		if (empty($results)){
			return null;
		}
		$event = Event::CreateFromData(reset($results));
		$this->postEventLoad($event);
		return $event;
	}

	/**
	 * @param int|null $maxCount
	 * @param int|null $offset
	 * @return array Event
	 * @throws
	 */
	public function loadRecentEvents($maxCount = null, $offset = null)
	{
		$limitSQL = '';
		if ($maxCount){
			$limitSQL = sprintf(' LIMIT %d', intval($maxCount));
			if ($offset){
				$limitSQL .= sprintf(" OFFSET %d", intval($offset));
			}
		}
		$sql = sprintf("SELECT * FROM events ORDER BY updated DESC %s", $limitSQL);
		$stmt = $this->getConnection()->prepare($sql);
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}
		$res = $stmt->get_result();
		$events = array();
		while ($row = $res->fetch_assoc()){
			$events[] = Event::CreateFromData($row);
		}
		$this->postEventLoad($events);
		return $events;
	}
	
	/**
	 * @param int $updated
	 * @param int|null $maxCount
	 * @param bool|null $older - defaults to false
	 * @return Event[]
	 * @throws
	 */
	public function loadEventsByUpdated($updated, $maxCount = null, $older = null)
	{
		$limitSQL = '';
		if ($maxCount){
			$limitSQL = sprintf(' LIMIT %d', intval($maxCount));
		}
		if ($older == null){
			$older = false;		
		}
		$operator = $older ? '<=' : '>=';
		$order = $older ? 'DESC' : 'ASC';
		$sql = sprintf("SELECT * FROM events WHERE UNIX_TIMESTAMP(updated) %s ? ORDER BY updated %s %s", $operator, $order, $limitSQL);
		$stmt = $this->getConnection()->prepare($sql);
		$this->bindParams($stmt, array(intval($updated)));
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}
		$res = $stmt->get_result();
		$events = array();
		while ($row = $res->fetch_assoc()){
			$events[] = Event::CreateFromData($row);
		}
		$this->postEventLoad($events);
		return $events;
	}

	/**
	 * @param string $name
	 * @return Performer
	 * @throws
	 */
	public function createPerformer($name)
	{
		$lookup = $this->loadPerformerByName($name);
		if ($lookup instanceof Performer){
			return $lookup;
		}
		$sql = "INSERT INTO performers (name, created) values (?, NOW())";
		$stmt = $this->getConnection()->prepare($sql);
		$stmt->bind_param('s', $name);
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}

		$performer = Performer::CreateNew($name);
		$performer->id = $stmt->insert_id;
		return $performer;
	}


        public function createPerformerWithId($id, $name)
        {
		$lookup = $this->loadPerformerByName($name);
                if ($lookup instanceof Performer){
                        return $lookup;
                }
 
		$sql = "INSERT INTO performers (id, name, created) values (?, ?, NOW())";
                $stmt = $this->getConnection()->prepare($sql);
                $stmt->bind_param('is', $id, $name);
                if (!$stmt->execute()){
                        throw new \Exception(sprintf('db failure %s', $stmt->error));
                }

                $performer = Performer::CreateNew($name);
                $performer->id = $id;
                return $performer;
        }


	/**
	 * @param array $ids
	 * @return array Performer
	 */
	public function loadPerformersByIds(array $ids)
	{
		$performers = array();
		$results = $this->fetchResultsByIds('performers', $ids);
		foreach ($results as $result){
			$event = Performer::CreateFromData($result);
			$performers[$event->getId()] = $event;
		}
		$this->postPerformerLoad($performers);
		return $performers;
	}

	/**
	 * @param string $name
	 * @return Performer
	 * @throws
	 */
	public function loadPerformerByName($name)
	{
		$results = $this->fetchResultsByKey('performers', 'name', $name);
		if (empty($results)){
			return null;
		}
		$performer = Performer::CreateFromData(reset($results));
		$this->postPerformerLoad($performer);
		return $performer;
	}

	/**
	 * @return array of Performer
	 * @throws
	 */
	public function loadAllPerformers()
	{
		$sql = "SELECT * FROM performers";
		$stmt = $this->getConnection()->prepare($sql);
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}
		$res = $stmt->get_result();
		$performers = array();
		while ($row = $res->fetch_assoc()){
			$performers[$row['id']] = Performer::CreateFromData($row);
		}
		$this->postPerformerLoad($performers);
		return $performers;
	}

	public function loadActivePerformerNames()
	{
		// we cant rely on using loadAllPerformers to do this; it causes funny behaviors
		$sql = "SELECT id, name FROM performers WHERE deleted = 0";
		$stmt = $this->getConnection()->prepare($sql);
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}
		$res = $stmt->get_result();
		$performers = array();
		while ($row = $res->fetch_assoc()){
			$performers[$row['id']] = $row['name'];
		}
		return $performers;
	}

	/**
	 * @inheritdoc
	 */
	public function setPerformerHighlight(Performer $performer, $highlight)
	{
		$this->updateRecord('performers', $performer->getId(), array('highlight' => $highlight ? true : false));
	}
	
	/**
	 * @param $id
	 * @return bool
	 */
	public function deletePerformer($id)
	{
		$this->updateRecord('performers', $id, array('deleted' => 1));
	}

	/**
	 * @param array $performers		Array of Performer
	 */
	public function savePerformers(array $performers)
	{
		foreach ($performers as $performer){
			$this->savePerformer($performer);
		}
	}

	public function savePerformer(Performer $performer)
	{
		$updates = array(
			'name' => $performer->getName(),
			'imageUrl' => $performer->getImageUrl(),
			'highlight' => $performer->isHighlighted(),
			'deleted' => $performer->isDeleted(),
			'site_urls' => join(',', $performer->getSiteUrls()),
		);
		$rv = $this->updateRecord('performers', $performer->getId(), $updates);

		return $rv;
	}
	
	/**
	 * Used to mark that an Performer was updated now
	 * @param Performer $performer
	 * @throws \Exception
	 */
	protected function markPerformerUpdated(Performer $performer)
	{
		$sql = "UPDATE performers SET updated = NOW() WHERE id = ?";
		$stmt = $this->getConnection()->prepare($sql);
		$this->bindParams($stmt, array($performer->getId()));
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}
	}

	public function addPerformerToEvent($performer, $event)
	{
		$performer = $this->getPerformerFromId($performer);
		$event = $this->getEventFromId($event);

		if (in_array($performer->getId(), $event->getPerformerIds())){
			// don't add twice
			return;
		}

		$sql = "INSERT IGNORE INTO event_performers (event_id, performer_id) VALUES (?,?)";
		$stmt = $this->getConnection()->prepare($sql);
		$performerId = $performer->getId();
		$eventId = $event->getId();
		$stmt->bind_param('ii', $eventId, $performerId);
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}

		$this->markPerformerUpdated($performer);
		
		// update bookkeeping
		$event->addPerformer($performer);
		$performer->addEventId($event->getId(), true);
	}

	public function removePerformerFromEvent($performer, $event)
	{
		$performer = $this->getPerformerFromId($performer);
		$event = $this->getEventFromId($event);

		$sql = "DELETE FROM event_performers WHERE event_id = ? AND performer_id = ?";
		$stmt = $this->getConnection()->prepare($sql);
		$eventId = $event->getId();
		$pId = $performer->getId();
		$stmt->bind_param('ii', $eventId, $pId);
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}

		// update bookkeeping
		$event->removePerformer($performer);
		$performer->removeEvent($event);
	}

	/**
	 * Load all data for the given array of event objects
	 * This is really inefficient as some of this data may not be needed for every use-case
	 * Unfortunately the data-model does not support load-on-demand yet
	 * @param array|Event $events
	 */
	protected function postEventLoad($events)
	{
		if (!is_array($events)){
			$events = array($events);
		}
		if (empty($events)){
			return;
		}

		$eventsById = array();
		foreach ($events as $event){
			/** @var Event $event */
			$eventId = $event->getId();
			$eventsById[$eventId] = $event;
		}
		$eventIds = array_keys($eventsById);

		// load assets
		$assets = $this->fetchResultsByIds('event_assets', $eventIds, 'event_id');
		foreach ($assets as $row){
			$eventAsset = EventAsset::CreateFromData($row);
			$eventId = $row['event_id'];
			$event = $eventsById[$eventId];
			$event->addAssets($eventAsset);
		}

		// load sub urls
		$subUrls = $this->fetchResultsByIds('event_sub_urls', $eventIds, 'event_id');
		foreach ($subUrls as $row){
			$eventId = $row['event_id'];
			$url = $row['url'];
			$event = $eventsById[$eventId];
			$event->addSubUrls($url);
		}

		// load performers ids/names
		$performers = $this->fetchResultsByJoinIds('event_performers', 'performers', 'performer_id', 'event_id', $eventIds);
		foreach ($performers as $row){
			$eventId = $row['event_id'];
			$performer = Performer::CreateFromData($row);
			$event = $eventsById[$eventId];
			$event->addPerformer($performer);
		}
	}

	protected function postPerformerLoad($performers)
	{
		if (!is_array($performers)){
			$performers = array($performers);
		}
		if (empty($performers)){
			return;
		}

		$performersById = ArrayUtils::ReindexByMethod($performers, 'getId');
		$performerIds = array_keys($performers);

		// load event ids
		$events = $this->fetchResultsByIds('event_performers', $performerIds, 'performer_id');
		foreach ($events as $row){
			$performerId = $row['performer_id'];
			/** @var Performer $performer */
			$performer = $performersById[$performerId];
			$performer->addEventId($row['event_id']);
		}
	}

	/**
	 * @return \mysqli
	 */
	protected function getConnection()
	{
		if (!isset($this->conn)){
			$this->conn = new \mysqli($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
		}
		return $this->conn;
	}

	protected function fetchResultsByIds($table, array $ids, $idCol = null)
	{
		if ($idCol === null){
			$idCol = 'id';
		}

		$sqlBinds = join(', ', array_map(function(){return '?';}, $ids));

		$sql = sprintf("SELECT * FROM %s WHERE %s IN (%s)", $table, $idCol, $sqlBinds);
		$stmt = $this->getConnection()->prepare($sql);
		if ($stmt === false){
			throw new \Exception(sprintf('db failure [%s] from [%s] with [%s]', $this->getConnection()->error, $sql, print_r($ids,true)));
		}
		$this->bindParams($stmt, $ids);
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}
		$res = $stmt->get_result();
		$results = array();
		while ($row = $res->fetch_assoc()){
			$results[] = $row;
		}
		return $results;
	}

	protected function fetchResultsByKey($table, $keyCol, $keyVal)
	{
		$sql = sprintf("SELECT * FROM `%s` WHERE `%s` = ?", $table, $keyCol);
		$stmt = $this->getConnection()->prepare($sql);
		
		$this->bindParams($stmt, array($keyVal));
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}
		$res = $stmt->get_result();
		$results = array();
		while ($row = $res->fetch_assoc()){
			$results[] = $row;
		}
		return $results;
	}

	protected function fetchResultsByJoinIds($joinTable, $dataTable, $joinCol, $idCol2, array $ids)
	{
		$sqlBinds = join(', ', array_map(function(){return '?';}, $ids));

		$sql = sprintf(
			"SELECT a.%s AS %s, b.* FROM %s a LEFT JOIN %s b ON (a.`%s` = b.id) WHERE a.`%s` IN (%s)",
			$idCol2, $idCol2, $joinTable, $dataTable, $joinCol, $idCol2, $sqlBinds
		);
		$stmt = $this->getConnection()->prepare($sql);
		if ($stmt === false){
			throw new \Exception(sprintf('db prepare failure [%s] from [%s]', $this->getConnection()->error, $sql));
		}
		$this->bindParams($stmt, $ids);
		if (!$stmt->execute()){
			throw new \Exception(sprintf('db failure %s', $stmt->error));
		}
		$res = $stmt->get_result();
		$results = array();
		while ($row = $res->fetch_assoc()){
			$results[] = $row;
		}
		return $results;
	}

	protected function updateRecord($table, $idVal, array $updates, $idCol = null)
	{
		if ($idCol === null){
			$idCol = 'id';
		}
		$sql = sprintf("UPDATE %s SET ", $table);
		$sqlUpdates = array_map(function ($k){ return sprintf('`%s` = ?', $k); }, array_keys($updates));
		$sql .= join(',', $sqlUpdates);
		$updates[$idCol] = $idVal;
		$sql .= sprintf(' WHERE `%s` = ?', $idCol);
		$stmt = $this->getConnection()->prepare($sql);
		if ($stmt === false){
        		throw new \Exception(sprintf('db failure %s from %s', $this->getConnection()->error, $sql));
		}
		$this->bindParams($stmt, $updates);
		return $stmt->execute();
	}

	protected function maintainN2N($table, $parentId, $parentIdCol, array $childIdsWanted, $childIdCol)
	{
		// first pull all existing links for parent
		$sql = sprintf("SELECT * FROM %s WHERE %s = ?", $table, $parentIdCol);
		$results = $this->fetchResultsByKey($table, $parentIdCol, $parentId);
		$childrenHave = array_keys(ArrayUtils::ReindexByMethod($results, $childIdCol));
		// TODO: finish
	}

	protected function bindParams(\mysqli_stmt $stmt, array $values)
	{
	    $bindStr = '';
	    foreach ($values as $k => $value){
 	        if (is_string($value)){
        	        $bindStr .= 's';
            	} else if (is_bool($value)){
                	$values[$k] = $value ? 1 : 0;
                	$bindStr .= 'i';
            	} else if (is_int($value)){
                	$bindStr .= 'i';
		} else if (is_null($value)){
			$bindStr .= 'i';
 	        } else {
        		throw new \Exception(sprintf('Unsupported value type %s', gettype($value)));
            	}
	    }
		
		$args = array_values($values);
		array_unshift($args, $bindStr);
		call_user_func_array(array($stmt, 'bind_param'), refValues($args));
	}
}

function refValues($arr){
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
    {
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
    return $arr;
}

