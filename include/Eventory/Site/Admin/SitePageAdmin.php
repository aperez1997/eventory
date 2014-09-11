<?php

namespace Eventory\Site\Admin;

use Eventory\Site\Constants\SitePageParams;
use Eventory\Site\SitePageBase;

abstract class SitePageAdmin extends SitePageBase
{
	protected function isAdminPage(){ return true; }
	
	protected function loadEvent($params, &$eventId = null)
	{
		$eventId = $params[SitePageParams::EVENT_ID];
		$event = $this->store->loadEventById($eventId);
		return $event;
	}
}