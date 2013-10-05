<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Site\Browse;

use Eventory\Site\SitePageBase;

class SiteBrowseRecentEvents extends SitePageBase
{
	public function render(array $params)
	{
		$maxPerPage = 100;
		$offset = 0;

		if (isset($params['o'])){
			$offset = $params['o'];
		}

		$events = $this->store->loadRecentEvents($maxPerPage, $offset);

		$content = $this->renderContent($this->getTemplatesPath() . 'tmp_browse_recent_events.php', $events);

		return $this->renderMain($content);
	}
}