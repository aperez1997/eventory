<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Storage;

use Eventory\Objects\Event\Event;
use Eventory\Objects\Performers\Performer;

interface iStorageProvider
{
	/**
	 * @param $url
	 * @param $key
	 * @return Event
	 */
	public function createEvent($url, $key);

	/**
	 * @deprecated
	 * @param array $events		array of Event
	 */
	public function saveEvents(array $events);

	/**
	 * @param int|Event $eventId
	 * @param array $assets         Array of EventAsset
	 */
	public function addAssetsToEvent($eventId, array $assets);

	/**
	 * @param int|Event $eventId
	 * @param array $subUrls        Array of string
	 */
	public function addSubUrlsToEvent($eventId, array $subUrls);

	/**
	 * @param array $ids
	 * @return array Event
	 */
	public function loadEventsById(array $ids);

    /**
     * @param int $eventId
     * @return Event
     */
	public function loadEventById($eventId);

	/**
	 * @param string $key
	 * @return Event
	 */
	public function loadEventByKey($key);

	/**
	 * @param int|null $maxCount
	 * @param int|null $offset
	 * @return Event[]
	 */
	public function loadRecentEvents($maxCount = null, $offset = null);

	/**
	 * @param int $updated
	 * @param int|null $maxCount
	 * @param bool|null $older - defaults to false
	 * @return Event[]
	 */
	public function loadEventsByUpdated($updated, $maxCount = null, $older = null);
	
	/**
	 * Creates a performer with the given, or returns the existing one if one already exists
	 * @param string $name
	 * @return Performer
	 */
	public function createPerformer($name);

	/**
	 * @param array $ids
	 * @return Performer[]
	 */
	public function loadPerformersByIds(array $ids);

	/**
	 * @param string $performerId
	 * @return Performer|null
	 */
	public function loadPerformerById($performerId);

	/**
	 * @param string $name
	 * @return Performer|null
	 */
	public function loadPerformerByName($name);

	/**
	 * @deprecated - should replace with with loadActivePerformerNames, unless we need event count. then we'll want a new method
	 * @return Performer[]
	 */
	public function loadAllPerformers();

	/**
	 * @return string[] Array of id => name
	 */
	public function loadActivePerformerNames();

	/**
	 * Use to change the highlight value of the given performer
	 * @param Performer $performer
	 * @param bool $highlight
	 * @return
	 */
	public function setPerformerHighlight(Performer $performer, $highlight);
	
	/**
	 * @param $id
	 * @return bool
	 */
	public function deletePerformer($id);

	/**
	 * @deprecated
	 * @param array $performers		Array of Performer
	 */
	public function savePerformers(array $performers);

	/**
	 * @param int|Performer $performer
	 * @param int|event $event
	 */
	public function addPerformerToEvent($performer, $event);

	/**
	 * @param int|Performer $performer
	 * @param int|event $event
	 */
	public function removePerformerFromEvent($performer, $event);
}
