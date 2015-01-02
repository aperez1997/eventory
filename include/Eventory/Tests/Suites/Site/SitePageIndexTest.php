<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2015 Zoosk Inc.
 */

namespace Suites\Site;

use Eventory\Site\Constants\SitePageParams;
use Eventory\Site\Constants\SitePageType;
use Eventory\Site\SitePageIndex;
use Eventory\Tests\EventoryTestCase;

class SitePageIndexTest extends EventoryTestCase
{
	public function testRender()
	{
		foreach (SitePageType::GetAll() as $type){
			$params = array(
				SitePageIndex::KEY_PAGE => $type, 
				SitePageParams::EVENT_ID => 1,
				SitePageParams::PERFORMER_ID => 1,    
			);
			
			$page = new SitePageIndex($this->getStorageProvider());
			$content = $page->render($params);
			$this->assertTrue(is_string($content));
		}
		
	}
} 