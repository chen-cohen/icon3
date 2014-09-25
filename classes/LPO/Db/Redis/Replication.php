<?php

namespace LPO\Db\Redis;

use Redis;

class Replication {

	const MIN_ROW_COUNT  = 1000;
	const MAX_ROW_COUNT  = 100000;
	const BULK_DELIMITER = PHP_EOL;

	/**
	 * @param string $redisKey
	 * @param string $dbName
	 * @param string $tableName
	 * @param array $columns
	 *
	 * @return bool
	 */
	public function replicate($redisKey, $dbName, $tableName, array $columns)
	{
		foreach(Buffer::$sockets as $socket)
		{
			$redis = Buffer::getSpecificConnection($socket);
			$this->_replicate($redis, $redisKey, $dbName, $tableName, $columns);
		}
		return true;
	}

	/**
	 * @param Redis $redis
	 * @param string $redisKey
	 * @param string $dbName
	 * @param string $tableName
	 * @param array $columns
	 *
	 * @return bool
	 */
	protected function _replicate(Redis $redis, $redisKey, $dbName, $tableName, array $columns)
	{
		$length = $redis->lLen($redisKey);

		if($length < static::MIN_ROW_COUNT)
		{
			return false; // not worth it
		}

//		$sql = $this->_getInsertStatement($tableName, $columns);
		$sql = '';

		// fetch results from Redis
		$length  = ($length > static::MAX_ROW_COUNT) ? static::MAX_ROW_COUNT : $length;
		$results = $redis->lRange($redisKey, 0, $length);
		// merge
		$sql .= $this->_getFormattedRow($results);

		$dir = implode(DS, array('', 'storage', $dbName, $tableName));
		@mkdir($dir, 0777, true);

		$targetFilename = $dir . DS . $tableName . '.' . str_replace('.','',microtime(true));

		$successful = file_put_contents($targetFilename, $sql);

		if($successful)
		{
			$redis->lTrim($redisKey, $length, -1);
		}
		return true;
	}

	/**
	 * @param $tableName
	 * @param array $columns
	 *
	 * @return string
	 */
	protected function _getInsertStatement($tableName, array $columns)
	{
		return implode(',', $columns) . PHP_EOL;
	}

	/**
	 * @param $results
	 *
	 * @return string
	 */
	protected function _getFormattedRow($results)
	{
		return implode(self::BULK_DELIMITER, $results);
	}
}