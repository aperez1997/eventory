<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 */

namespace Eventory\Site\View;

use Eventory\Site\Constants\SitePageParams;
use Eventory\Site\SitePageBase;

class SiteViewPerformer extends SitePageBase
{
	/**
	 * @param array $params
	 * @return string HTML
	 */
	public function render(array $params)
	{
		$pId = $params[SitePageParams::PERFORMER_ID];

		$performer = $this->store->loadPerformerById($pId);

		$content = $this->renderContent($this->getTemplatesPath() . 'tmp_view_performer.php', $performer);
		return $this->renderMain($content, 'E: '.$performer->getName());
	}
}
