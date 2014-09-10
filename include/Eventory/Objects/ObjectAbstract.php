<?php

namespace Eventory\Objects;

class ObjectAbstract
{
	protected function convertTinyIntToBool($field)
	{
		$value = $this->$field;
		if (!is_bool($value)){
			$this->$field = $value == 0 ? false : true;
		}
	}
	
	protected function convertDateStringToTime($field)
	{
		$value = $this->$field;
		if (!is_int($value)){
			$this->$field = strtotime($value . ' UTC');
		}
	}

	protected function loadFromData($data)
	{
		foreach ($this as $k => $v){
			if (isset($data[$k])){
				$this->$k = $data[$k];
			}
		}
	}
}
