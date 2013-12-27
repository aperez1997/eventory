<?php

namespace Eventory\Site\Browse;

use Eventory\Objects\Performers\Performer;
use Eventory\Site\Constants\SitePageParams;
use Eventory\Site\SitePageBase;
use Eventory\Utils\ArrayUtils;

class SiteBrowsePerformers extends SitePageBase
{
	const SORT_DEFAULT		= 'Default';
	const SORT_ALPHA 		= 'Alpha';
	const SORT_EVENTS		= 'Event Count';

	public function render(array $params)
	{
		$performers = $this->store->loadAllPerformers();

		// sort by sort key
		$sortParam = ArrayUtils::ValueForKey($params, SitePageParams::SORT, self::SORT_DEFAULT);
		$performers = $this->handleSort($performers, $sortParam);

		// remove deleted performers
		$performers = array_filter($performers, array($this, 'notDeleted'));

		$links = array();
		foreach (array(self::SORT_DEFAULT, self::SORT_ALPHA, self::SORT_EVENTS) as $sort){
			if ($sort == $sortParam){
				$url = null;
			} else {
				$url = $this->getLinkBrowsePerformers($sort);
			}
			$links[] = array($sort, $url);
		}

		$content = $this->renderContent($this->getTemplatesPath() . 'tmp_browse_performers.php', array($performers, $links));

		return $this->renderMain($content);
	}

	protected function handleSort($performers, $sort)
	{
		switch ($sort){
			case self::SORT_ALPHA:
				$performers = ArrayUtils::ReindexByMethod($performers, 'getName');
				ksort($performers);
				break;
			case self::SORT_EVENTS:
				$performers = ArrayUtils::ReindexByMethod($performers, 'getEventCount');
				ksort($performers);
				$performers = array_reverse($performers);
				break;
			case self::SORT_DEFAULT:
			default:
				$performers = ArrayUtils::ReindexByMethod($performers, 'getSortKey');
				ksort($performers);
				$performers = array_reverse($performers);
				break;
		}
		return $performers;
	}

	protected function notDeleted(Performer $performer)
	{
		return !$performer->isDeleted();
	}
}