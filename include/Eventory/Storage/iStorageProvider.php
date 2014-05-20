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
	 * @param array $events		array of Event
	 */
	public function saveEvents(array $events);

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
	 * @return array Event
	 */
	public function loadRecentEvents($maxCount = null, $offset = null);

	/**
	 * @param string $name
	 * @return Performer
	 */
	public function createPerformer($name);

	/**
	 * @param array $ids
	 * @return array Performer
	 */
	public function loadPerformersByIds(array $ids);

	/**
	 * @param string $performerId
	 * @return Performer
	 */
	public function loadPerformerById($performerId);

	/**
	 * @param string $name
	 * @return Performer
	 */
	public function loadPerformerByName($name);

	/**
	 * @return array of Performer
	 */
	public function loadAllPerformers();

	/**
	 * @return array of strings
	 */
	public function loadActivePerformerNames();

	/**
	 * @param $id
	 * @return bool
	 */
	public function deletePerformer($id);

	/**
	 * @param array $performers		Array of Performer
	 */
	public function savePerformers(array $performers);
}
