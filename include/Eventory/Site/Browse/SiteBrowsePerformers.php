<?php

namespace Eventory\Site\Browse;

use Eventory\Objects\Performers\Performer;
use Eventory\Site\SitePageBase;
use Eventory\Utils\ArrayUtils;

class SiteBrowsePerformers extends SitePageBase
{
	public function render(array $params)
	{
		$performers = $this->store->loadAllPerformers();

		// sort by sort key
		$performers = ArrayUtils::ReindexByMethod($performers, 'getSortKey');
		ksort($performers);
		$performers = array_reverse($performers);

		// remove deleted performers
		$performers = array_filter($performers, array($this, 'notDeleted'));

		$content = $this->renderContent($this->getTemplatesPath() . 'tmp_browse_performers.php', $performers);

		return $this->renderMain($content);
	}

	protected function notDeleted(Performer $performer)
	{
		return !$performer->isDeleted();
	}
}