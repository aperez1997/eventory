<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

use Eventory\Objects\EventScrapeItem;
use Eventory\Tests\EventoryTestCase;

class EventScrapeItemTest extends EventoryTestCase
{
	public function testBasic()
	{
		$id = uniqid();

		$scrapeItem = new EventScrapeItem();
		$scrapeItem->eventKey = $id;
		$this->assertTrue(true);
	}
}