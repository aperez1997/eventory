<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

namespace Eventory\Site\Browse;

use Eventory\Site\Constants\SitePageParams;
use Eventory\Site\SitePageBase;

class SiteBrowseRecentEvents extends SitePageBase
{
	public function render(array $params)
	{
		$maxPerPage = 100;
		$offset = 0;

		$paramOffset = SitePageParams::OFFSET;
		if (isset($params[$paramOffset])){
			$offset = $params[$paramOffset];
		}

		$events = $this->store->loadRecentEvents($maxPerPage, $offset);

		$next = $this->getLinkRecentEvents($offset + $maxPerPage);

		$vars = array(
			'e' => $events,
			'n' => $next,
		);

		if ($offset != 0){
			$vars['p'] = $this->getLinkRecentEvents($offset - $maxPerPage);
		}

		$content = $this->renderContent($this->getTemplatesPath() . 'tmp_browse_recent_events.php', $vars);

		return $this->renderMain($content);
	}
}
