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
}