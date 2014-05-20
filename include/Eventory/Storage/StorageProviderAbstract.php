<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2014 Zoosk Inc.
 */

namespace Eventory\Storage;

use Eventory\Objects\Event\Event;
use Eventory\Objects\Performers\Performer;
use Eventory\Utils\ArrayUtils;

abstract class StorageProviderAbstract implements iStorageProvider
{
	/**
	 * @param int $eventId
	 * @return Event
	 */
	public function loadEventById($eventId)
	{
		$events = $this->loadEventsById(array($eventId));
		return ArrayUtils::ValueForKey($events, $eventId);
	}

	/**
	 * @param $performerId
	 * @return Performer
	 */
	public function loadPerformerById($performerId)
	{
		$performers = $this->loadPerformersByIds(array($performerId));
		return ArrayUtils::ValueForKey($performers, $performerId);
	}

	/**
	 * @return array of strings
	 */
	public function loadActivePerformerNames()
	{
		$performers = $this->loadAllPerformers();
		$fn = function(Performer $p){ return !$p->isDeleted(); };
		$performers = array_filter($performers, $fn);
		return array_map(function(Performer $p){ return $p->getName(); }, $performers);
	}
}