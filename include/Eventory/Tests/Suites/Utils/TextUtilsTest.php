<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>

 */

use Eventory\Tests\EventoryTestCase;
use Eventory\Utils\TextUtils;

class TextUtilsTest extends EventoryTestCase
{
	public function testBasic()
	{
		$name1 = 'Peter Griffin';
		$name2 = 'Justin Beber';
		$text = "blah blah {$name1} blah FASDfs blah Blah caw= {$name2}";
		$names = TextUtils::FindNamesInText($text);

		$this->assertEquals(2, count($names));
		$this->assertTrue(in_array($name1, $names));
		$this->assertTrue(in_array($name2, $names));
	}
}