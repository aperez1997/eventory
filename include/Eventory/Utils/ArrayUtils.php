<?php

namespace Eventory\Utils;

class ArrayUtils
{
	public static function ValueForKey($array, $key, $default = null)
	{
		if (isset($array[$key])){
			return $array[$key];
		}
		return $default;
	}

	public static function ReindexByMethod(array $list, $method)
	{
		$newList = array();
		foreach ($list as $v){
			$k2 = $v->$method();
			$newList[$k2] = $v;
		}
		return $newList;
	}

	public static function ReindexByFunction(array $list, $callable)
	{
		$newList = array();
		foreach ($list as $v){
			$key = $callable($v);
			$newList[$key] = $v;
		}
		return $newList;
	}
}