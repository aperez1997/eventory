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
}