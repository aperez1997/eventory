<?php
namespace Eventory\Site\Browse;

use Eventory\Objects\Event\Event;
use Eventory\Site\Constants\SitePageParams;
use Eventory\Site\SitePageBase;

class SiteBrowseEventsUpdatedSince extends SitePageBase
{
	public function render(array $params)
	{
		$maxPerPage = 50;
		$offset = 0;

		$paramOffset = SitePageParams::OFFSET;
		if (isset($params[$paramOffset])){
			$offset = intval($params[$paramOffset]);
		}		
		if ($offset <= 0){
			$offset = time() - 86400;
		}
		$offsetStr = date("Y-n-j g:i:sa", $offset);

		$events = $this->store->loadEventsByUpdated($offset, $maxPerPage + 1);

		$vars = array(
			'e' => $events,
			'u' => $offsetStr,
		);

		$lastEvent = end($events);
		if (count($events) > $maxPerPage && $lastEvent instanceof Event){
			$vars['n'] = $this->getLinkEventsUpdatedSince($lastEvent->getUpdated());
		}

		$content = $this->renderContent($this->getTemplatesPath() . 'tmp_browse_events_updated_since.php', $vars);

		return $this->renderMain($content, sprintf('E:Events b4 %s', $offsetStr));
	}
}
