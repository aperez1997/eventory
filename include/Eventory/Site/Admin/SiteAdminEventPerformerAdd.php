<?php

namespace Eventory\Site\Admin;

use Eventory\Site\Constants\SitePageParams;
use Eventory\Site\SitePageBase;

class SiteAdminEventPerformerAdd extends SitePageBase
{
	public function render(array $params)
	{
		$eventId = $params[SitePageParams::EVENT_ID];

		$events = $this->store->loadEventsById(array($eventId));
		$event = reset(array_values($events));

		$content = $this->renderContent($this->getTemplatesPath() . 'tmp_event_performer_add.php', $event);

		return $this->renderMain($content);
	}
} 