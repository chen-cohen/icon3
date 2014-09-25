<?php

namespace LPO;

use LPO\Db\Redis\Buffer;
use LPO\Persistence\InjectableType;

class Injectable {

	use Attributable;

	const REDIS_KEY = 'lpo_injectable_setting';
	const REDIS_KEY_DELIMITER = '_';

	/**
	 * @return int
	 */
	public function getValue()
	{
		$buffer = Buffer::getConnection();
		$keys = array();

		$permutationKeys = $this->_getPermutationKeys();
		$values = $buffer->hMGet(static::REDIS_KEY,array_keys($permutationKeys));
		foreach($values as $key=>$value)
		{
			if(!$value){continue;}
			$keys[$key] = $value;
		}

		$value = $this->_chooseFromAttributeConflict($keys, static::REDIS_KEY_DELIMITER);
		return ($value) ? (int)$value : InjectableType::_DEFAULT;
	}

}