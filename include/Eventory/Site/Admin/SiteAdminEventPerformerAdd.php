<?php

namespace Eventory\Site\Admin;

use Eventory\Objects\Event\Event;
use Eventory\Objects\Performers\Performer;
use Eventory\Site\Constants\SitePageParams;
use Eventory\Site\SitePageBase;

class SiteAdminEventPerformerAdd extends SitePageBase
{
	/**
	 * @param array $params
	 * @return bool $result
	 */
	public function post(array $params)
	{
		$event = $this->loadEvent($params, $eventId);
		if (!$event instanceof Event){
			$this->setPostStatus(false, sprintf('Event not found [%s]', $eventId));
			return false;
		}

		$type = $params[SitePageParams::TYPE];
		if ($type == 'pick'){
			$performerId = $params[SitePageParams::PICK];
			$performer = $this->store->loadPerformerById($performerId);

		} else if ($type == 'enter'){

			$name = $params[SitePageParams::TEXT];
			if (empty($name)){
				$this->setPostStatus(false, sprintf('no name entered', $type));
				return false;
			}
			$performer = $this->store->createPerformer($name);
		} else {
			$this->setPostStatus(false, sprintf('Please select a type'));
			return false;
		}

		if (!$performer instanceof Performer){
			$this->setPostStatus(false, sprintf('Performer not created!'));
			return false;
		}

		$event->addPerformer($performer);
		$this->store->saveEvents(array($event));
		$this->store->savePerformers(array($performer));

		$this->setPostStatus(true, sprintf('Performer [%s] added to this event', $performer->getName()));

		return true;
	}

	public function render(array $params)
	{
		$event = $this->loadEvent($params);

		$content = $this->renderContent($this->getTemplatesPath() . 'tmp_event_performer_add.php', $event);

		return $this->renderMain($content);
	}

	protected function loadEvent($params, &$eventId = null)
	{
		$eventId = $params[SitePageParams::EVENT_ID];
		$events = $this->store->loadEventsById(array($eventId));
		$event = reset(array_values($events));
		return $event;
	}
} 