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
			$event->addPerformer($performer);
		}
		// no save here
	}


	public function removePerformerIdFromEventId($performerId, $eventId)
	{
		$performer	= $this->store->loadPerformerById($performerId);
		$event		= $this->store->loadEventById($eventId);
		if (!$performer instanceof Performer){
                        throw new \Exception(sprintf('Invalid performer id [%s]', $performerId));
                }
		if (!$event instanceof Event){
			 throw new \Exception(sprintf('Invalid event id [%s]', $eventId));
		}
		$this->removePerformerFromEvent($performer, $event);
	}

	public function removePerformerFromEvent(Performer $performer, Event $event)
	{
		$event->removePerformer($performer);
		$performer->removeEvent($event);

                $this->store->saveEvents(array($event));
                $this->store->savePerformers(array($performer));		
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
	 * @return array Event
	 */
	public function markPerformerDuplicate(Performer $dupePerformer, Performer $realPerformer)
	{
		$eventIds = $dupePerformer->getEventIds();
		$events = $this->store->loadEventsById($eventIds);
		foreach ($events as $event){
			/** @var Event $event */
			$event->addPerformer($realPerformer);
			$event->removePerformer($dupePerformer);
		}
		$this->store->deletePerformer($dupePerformer->getId());

		$this->store->saveEvents($events);
		$this->store->savePerformers(array($realPerformer, $dupePerformer));

		return $events;
	}
}
