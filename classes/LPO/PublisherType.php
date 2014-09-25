<?php

namespace LPO;

use LPO\Db\Redis\Buffer;

class PublisherType {

	const REDIS_KEY = 'lpo_publisher_type';

	/**
	 * @param int $id
	 * @return string
	 */
	public static function getNameFromId($id)
	{
		return Buffer::getConnection()->hGet(static::REDIS_KEY,(string)$id);
	}
}