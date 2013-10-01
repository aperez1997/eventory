<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Storage;

use Eventory\Objects\Event\Event;

interface iStorageProvider
{
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
	 * @param $performerId
	 * @return array Event
	 */
	public function loadEventsByPerformer($performerId);

	/**
	 * @param int|null $maxCount
	 * @param int|null $offset
	 * @return array Event
	 */
	public function loadRecentEvents($maxCount = null, $offset = null);
}