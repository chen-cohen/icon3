<?php
namespace LPO;

use LPO\Db\Redis\Buffer;

class Configuration {

	const REDIS_KEY = 'lpo_config';
	const REDIS_KEY_DELIMITER = '#';

	/**
	 * @param string $key
	 * @param Publisher $publisher
	 *
	 * @return string
	 */
	public static function getValue($key,Publisher $publisher)
	{
		$buffer = Buffer::getConnection();
		$value = $buffer->hGet(static::REDIS_KEY,$key.static::REDIS_KEY_DELIMITER.$publisher->getId());
		if(!$value)
		{
			$value = $buffer->hGet(static::REDIS_KEY,$key.static::REDIS_KEY_DELIMITER);
		}
		return $value;
	}
}