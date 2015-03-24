<?php

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

	public function loadActivePerformerNames()
	{
		$performers = $this->loadAllPerformers();
		$fn = function(Performer $p){ return !$p->isDeleted(); };
		/** @var Performer[] $performers */
		$performers = array_filter($performers, $fn);
		$result = array();
		foreach ($performers as $performer){
			$result[$performer->getId()] = $performer->getName();			
		}
		return $result;
	}

	/**
	 * @param int|Event $eventId
	 * @return Event
	 * @throws \Exception
	 */
	protected function getEventFromId($eventId)
	{
		if ($eventId instanceof Event){
			return $eventId;
		}
		$event = $this->loadEventById($eventId);
		if (!$event instanceof Event){
			throw new StorageException(sprintf('Invalid Event id [%s]', $eventId));
		}
		return $event;
	}

	/**
	 * @param int|Performer $performerId
	 * @return Performer
	 * @throws \Exception
	 */
	protected function getPerformerFromId($performerId)
	{
		if ($performerId instanceof Performer){
			return $performerId;
		}
		$performer = $this->loadPerformerById($performerId);
		if (!$performer instanceof Performer){
			throw new StorageException(sprintf('Invalid performer id [%s]', $performerId));
		}
		return $performer;
	}
}
