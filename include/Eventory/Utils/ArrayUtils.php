<?php
/**
 * @author Tony Perez <tonyp@zoosk.com>
 * @copyright Copyright (c) 2007-2013 Zoosk Inc.
 */

namespace Eventory\Utils;

class ArrayUtils
{
	public static function ReindexByMethod(array $list, $method)
	{
		$newList = array();
		foreach ($list as $k => $v){
			$k2 = $v->$method();
			$newList[$k2] = $v;
		}
		return $newList;
	}
}