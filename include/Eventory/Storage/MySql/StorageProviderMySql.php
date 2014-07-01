<?php

namespace Eventory\Storage\MySql;

use Eventory\Objects\Event\Assets\EventAsset;
use Eventory\Objects\Event\Event;
use Eventory\Objects\Performers\Performer;
use Eventory\Storage\iStorageProvider;
use Eventory\Storage\StorageProviderAbstract;
use Eventory\Utils\ArrayUtils;

/**
 * TODO: performer site urls, updated updating
 */
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
		$sql = "INSERT INTO events (url, key, created) values (?, ?, NOW())";
		$stmt = $this->getConnection()->prepare($sql);
		$stmt->bind_param('s', $url);
		$stmt->bind_param('s', $key);
		if (!$stmt->execute()){
			throw new \Exception('db failure');
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
			// TODO: remove this line
			'updated' => $event->getUpdated()
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
		$this->bindParam($stmt, $event->getId());
		if (!$stmt->execute()){
			throw new \Exception('db failure');
		}
	}

	/**
	 * @param int|Event $eventId
	 * @param array $assets         Array of EventAsset
	 */
	public function addAssetsToEvent($eventId, array $assets)
	{
		$event = $this->getEventFromId($eventId);

		$existingAssets = $event->getAssets();
		$existingAssets = ArrayUtils::ReindexByProperty($existingAssets, 'key');

		$changed = false;
		foreach ($assets as $asset){
			/** @var EventAsset $asset */
			if (array_key_exists($asset->key, $existingAssets)){
				// skip any assets we already have
				continue;
			}
			$changed = true;

			$sql = "INSERT INTO event_assets (event_id, key, type, hostUrl, imageUrl, linkUrl, text)
					VALUES (?, ?, ?, ?, ?, ?, ?)";
			$stmt = $this->getConnection()->prepare($sql);
			$this->bindParam($stmt, $event->getId());
			$this->bindParam($stmt, $asset->key);
			$this->bindParam($stmt, $asset->type);
			$this->bindParam($stmt, $asset->hostUrl);
			$this->bindParam($stmt, $asset->imageUrl);
			$this->bindParam($stmt, $asset->linkUrl);
			$this->bindParam($stmt, $asset->text);
			$stmt->execute();
		}
		$event->addAssets($assets);
		if ($changed){
			$this->markEventUpdated($event);
		}
	}

	/**
	 * @param int|Event $eventId
	 * @param array $subUrls        Array of string
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
			$this->bindParam($stmt, $event->getId());
			$this->bindParam($stmt, $subUrl);
			$stmt->execute();
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
		$this->postEventLoad($events);
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
			throw new \Exception('db failure');
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
			throw new \Exception('db failure');
		}

		$performer = Performer::CreateNew($name);
		$performer->id = $stmt->insert_id;
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
			throw new \Exception('db failure');
		}
		$res = $stmt->get_result();
		$performers = array();
		while ($row = $res->fetch_assoc()){
			$performers[] = Performer::CreateFromData($row);
		}
		$this->postPerformerLoad($performers);
		return $performers;
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function deletePerformer($id)
	{
		$this->updateRecord('performer', $id, array('deleted', 1));
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
		);
		$rv = $this->updateRecord('performers', $performer->getId(), $updates);

		// todo: siteUrls

		return $rv;
	}

	public function addPerformerToEvent($performer, $event)
	{
		$performer = $this->getPerformerFromId($performer);
		$event = $this->getEventFromId($event);

		if (in_array($performer->getId(), $event->getPerformerIds())){
			// don't add twice
			return;
		}

		$sql = "INSERT INTO event_performers (event_id, performer_id) VALUES (?,?)";
		$stmt = $this->getConnection()->prepare($sql);
		$stmt->bind_param('i', $performer->getId());
		$stmt->bind_param('i', $event->getId());
		if (!$stmt->execute()){
			throw new \Exception('db failure');
		}

		// update bookkeeping
		$event->addPerformer($performer);
		$performer->addEventId($event->getId());
	}

	public function removePerformerFromEvent($performer, $event)
	{
		$performer = $this->getPerformerFromId($performer);
		$event = $this->getEventFromId($event);

		$sql = "DELETE FROM event_performers WHERE event_id = ? AND performer_id = ?";
		$stmt = $this->getConnection()->prepare($sql);
		$stmt->bind_param('i', $performer->getId());
		$stmt->bind_param('i', $event->getId());
		if (!$stmt->execute()){
			throw new \Exception('db failure');
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
		if (!$idCol === null){
			$idCol = 'id';
		}

		$sqlBinds = join(', ', array_map(function(){return '?';}, $ids));

		$sql = sprintf("SELECT * FROM %s WHERE %s IN (%s)", $table, $idCol, $sqlBinds);
		$stmt = $this->getConnection()->prepare($sql);
		foreach ($ids as $id){
			$stmt->bind_param('i', $id);
		}
		if (!$stmt->execute()){
			throw new \Exception('db failure');
		}
		$res = $stmt->get_result();
		$results = array();
		while ($row = $res->fetch_assoc()){
			if (isset($row[$idCol])){
				$results[] = $res;
			} else {
				$results[] = $res;
			}
		}
		return $results;
	}

	protected function fetchResultsByKey($table, $keyCol, $keyVal)
	{
		$sql = sprintf("SELECT * FROM %s WHERE %s = ?", $table, $keyCol);
		$stmt = $this->getConnection()->prepare($sql);
		$this->bindParam($stmt, $keyVal);
		if (!$stmt->execute()){
			throw new \Exception('db failure');
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
			"SELECT b.* FROM %s a LEFT JOIN %s b ON (a.%s = b.%s) WHERE a.%s IN (%s)",
			$joinTable, $dataTable, $joinCol, $joinCol, $idCol2, $sqlBinds
		);
		$stmt = $this->getConnection()->prepare($sql);
		foreach ($ids as $id){
			$stmt->bind_param('i', $id);
		}
		if (!$stmt->execute()){
			throw new \Exception('db failure');
		}
		$res = $stmt->get_result();
		$results = array();
		while ($row = $res->fetch_assoc()){
			if (isset($row[$idCol2])){
				$results[] = $res;
			} else {
				$results[] = $res;
			}
		}
		return $results;
	}

	protected function updateRecord($table, $idVal, array $updates, $idCol = null)
	{
		if (!$idCol === null){
			$idCol = 'id';
		}
		$sql = sprintf("UPDATE %s SET ", $table);
		foreach ($updates as $k => $v){
			$sql .= sprintf('%s = ?', $k);
		}
		$sql .= sprintf('WHERE %s = ?', $idCol);
		$stmt = $this->getConnection()->prepare($sql);
		foreach ($updates as $v){
			$this->bindParam($stmt, $v);
		}
		$stmt->bind_param('i', $idVal);
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

	protected function bindParam(\mysqli_stmt $stmt, $value)
	{
		if (is_string($value)){
			$stmt->bind_param('s', $v);
		} else if (is_bool($value)){
			$val = $value ? 1 : 0;
			$stmt->bind_param('i', $val);
		} else if (is_int($value)){
			$stmt->bind_param('i', $v);
		} else {
			throw new \Exception(sprintf('Unsupported value type %s', gettype($value)));
		}
	}
}