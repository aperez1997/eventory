<?php

namespace Eventory\Model;

use Eventory\Objects\Event\Event;
use Eventory\Objects\Performers\Performer;
use Eventory\Storage\iStorageProvider;
use Eventory\Utils\TextUtils;

class EventModel
{
	protected $store;

	public function __construct(iStorageProvider $store)
	{
		$this->store = $store;
	}

	/**
	 * @param Event $event
	 */
	public function findPerformersForEvent(Event $event)
	{
		$description = $event->getDescription();
		$namesFound = TextUtils::FindNamesInText($description);

		$performerNames = $this->store->loadActivePerformerNames();
		foreach ($performerNames as $name){
			if (stripos($description, $name)){
				$namesFound[] = $name;
			}
		}

		foreach ($namesFound as $name){
			$performer = $this->store->createPerformer($name);
			$this->addPerformerToEvent($performer, $event);
		}
	}

	public function addPerformerToEvent(Performer $performer, Event $event)
	{
		$this->store->addPerformerToEvent($performer, $event);
	}

	public function addPerformerIdToEventId($performerId, $eventId)
	{
		$this->store->addPerformerToEvent($performerId, $eventId);
	}

	public function removePerformerFromEvent(Performer $performer, Event $event)
	{
		$this->store->removePerformerFromEvent($performer, $event);
	}

	public function removePerformerIdFromEventId($performerId, $eventId)
	{
		$this->store->removePerformerFromEvent($performerId, $eventId);
	}

	/**
	 * @param $dupePerformerId
	 * @param $realPerformerId
	 * @return array Event
	 * @throws \Exception
	 */
	public function markPerformerIdDuplicate($dupePerformerId, $realPerformerId)
	{
		$dupePerformer = $this->store->loadPerformerById($dupePerformerId);
		$realPerformer = $this->store->loadPerformerById($realPerformerId);
		if (!$dupePerformer instanceof Performer){
			throw new \Exception(sprintf('Invalid performer id [%s]', $dupePerformerId));
		}
		if (!$realPerformer instanceof Performer){
			throw new \Exception(sprintf('Invalid performer id [%s]', $realPerformerId));
		}
		return $this->markPerformerDuplicate($dupePerformer, $realPerformer);
	}

	/**
	 * @param Performer $dupePerformer
	 * @param Performer $realPerformer
	 * @return Event[]
	 */
	public function markPerformerDuplicate(Performer $dupePerformer, Performer $realPerformer)
	{
		$eventIds = $dupePerformer->getEventIds();
		$events = $this->store->loadEventsById($eventIds);
		foreach ($events as $event){
			/** @var Event $event */
			$this->addPerformerToEvent($realPerformer, $event);
			$this->removePerformerFromEvent($dupePerformer, $event);
		}
		$this->store->deletePerformer($dupePerformer->getId());

		return $events;
	}

	/**
	 * @param $performerId
	 * @throws \Exception
	 */
	public function togglePerformerIdHighlight($performerId)
	{
		$performer = $this->store->loadPerformerById($performerId);
		if (!$performer instanceof Performer){
			throw new \Exception(sprintf('Invalid performer id [%s]', $performerId));
		}		
		
		$this->togglePerformerHighlight($performer);
	}

	/**
	 * @param Performer $performer
	 */
	public function togglePerformerHighlight(Performer $performer)
	{
		$this->store->setPerformerHighlight($performer, !$performer->isHighlighted());
	}
}
