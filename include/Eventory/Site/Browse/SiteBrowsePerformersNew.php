<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2014 Zoosk Inc.
 */

namespace Eventory\Site\Browse;

use Eventory\Site\SitePageBase;

class SiteBrowsePerformersNew extends SitePageBase
{
	public function render(array $params)
	{
		$content = $this->renderContent($this->getTemplatesPath() . 'tmp_browse_performers_new.php', null);		
		return $this->renderContent($content, 'E: Performers New');
	}
}