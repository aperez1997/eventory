<?php

namespace Eventory\Site\Browse;

use Eventory\Objects\Performers\Performer;
use Eventory\Site\Constants\SitePageParams;
use Eventory\Site\SitePageBase;
use Eventory\Utils\ArrayUtils;

class SiteBrowsePerformers extends SitePageBase
{
	const SORT_DEFAULT		= Performer::SORT_DEFAULT;
	const SORT_ALPHA 		= Performer::SORT_ALPHA;
	const SORT_EVENTS		= Performer::SORT_EVENTS;

	public function render(array $params)
	{
		// sort by sort key
		$sortParam = ArrayUtils::ValueForKey($params, SitePageParams::SORT, self::SORT_DEFAULT);
		$performers = $this->performerModel->getPerformersForBrowse($sortParam);

		$links = array();
		foreach (array(self::SORT_DEFAULT, self::SORT_ALPHA, self::SORT_EVENTS) as $sort){
			if ($sort == $sortParam){
				$url = null;
			} else {
				$url = $this->getLinkBrowsePerformers($sort);
			}
			$links[] = array($sort, $url);
		}

		$content = $this->renderContent($this->getTemplatesPath() . 'tmp_browse_performers.php', array($performers, $links, $sortParam));

		return $this->renderMain($content, 'E: Performers');
	}
}
