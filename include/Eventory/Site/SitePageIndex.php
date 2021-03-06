<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 */

namespace Eventory\Site;

use Eventory\Site\Admin\SiteAdminEventPerformerAdd;
use Eventory\Site\Browse\SiteBrowseEventsUpdatedSince;
use Eventory\Site\Browse\SiteBrowsePerformers;
use Eventory\Site\Browse\SiteBrowsePerformersNew;
use Eventory\Site\Browse\SiteBrowseRecentEvents;
use Eventory\Site\Constants\SitePageParams;
use Eventory\Site\Constants\SitePageType;
use Eventory\Site\View\SiteViewPerformer;

class SitePageIndex extends SitePageBase
{
	const KEY_PAGE = SitePageParams::PAGE;

	public function render(array $params)
	{
		$page = SitePageType::DEFAULT_PAGE;
		if (isset($params[self::KEY_PAGE])){
			$page = $params[self::KEY_PAGE];
		}

		// TODO: move this site page creation into a factory
		switch ($page){
			case SitePageType::PERFORMER:
				$pageObject = new SiteViewPerformer($this->store);
				break;
			case SitePageType::BROWSE_PERFORMERS:
				$pageObject = new SiteBrowsePerformers($this->store);
				break;
			case SitePageType::BROWSE_PERFORMERS_NEW:
				$pageObject = new SiteBrowsePerformersNew($this->store);
				break;
			case SitePageType::EVENT_PERFORMER_ADD:
				$pageObject = new SiteAdminEventPerformerAdd($this->store);
				break;
			case SitePageType::EVENTS_UPDATED_SINCE:
				$pageObject = new SiteBrowseEventsUpdatedSince($this->store);
				break;
			case SitePageType::RECENT:
			default:
				$pageObject = new SiteBrowseRecentEvents($this->store);
				break;
		}

		if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
			$pageObject->post($_POST);
		}

		$pageObject->appendNavItems($this->navItems);

		return $pageObject->render($params);
	}
}
