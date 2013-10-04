<?php
/**
 * @author Tony Perez <aperez1997@yahoo.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Utils;

class TextUtils
{
	public static function FindNamesInText($text)
	{
		$regex = '/([A-Z][a-z]+ [A-Z][a-z]+)/';
		$rv = preg_match_all($regex, $text, $matches);
		if ($rv > 0){
			return $matches[0];
		} else {
			return array();
		}
	}
}