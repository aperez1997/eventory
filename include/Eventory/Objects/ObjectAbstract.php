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

	protected function loadFromData($data)
	{
		foreach ($this as $k => $v){
			if (isset($data[$k])){
				$this->$k = $data->$k;
			}
		}
	}
}