<?php

namespace LPO\Db\Redis;

use Redis;
use RedisException;

class Buffer {

	/**
	 * @var string[]
	 */
	public static $sockets = array(
		'/var/run/redis/redis.sock',
		'/var/run/redis/redis2.sock',
		'/var/run/redis/redis3.sock',
		'/var/run/redis/redis4.sock',
		'/var/run/redis/redis5.sock',
	);
	/**
	 * @var Redis
	 */
	protected static $_connection;
	/**
	 * @var Redis[]
	 */
	protected static $_specific_connections = array();

	/**
	 *
	 */
	final private function __construct(){ }

	/**
	 *
	 * @return Redis
	 */
	public static function getConnection()
	{
		if(!is_null(self::$_connection))
		{
			return self::$_connection;
		}

		$numberOfSockets = count(self::$sockets);
		$microTimeString = microtime();
		$randomInt       = (int)($microTimeString{2}.$microTimeString{3});
		$socketPath      = self::$sockets[$randomInt % $numberOfSockets];

		self::$_connection = self::_getRedisConnection($socketPath);

		return self::$_connection;
	}

	/**
	 * @param string $socketPath
	 *
	 * @return Redis
	 */
	public static function getSpecificConnection($socketPath)
	{
		if(!isset(self::$_specific_connections[$socketPath]))
		{
			self::$_specific_connections[$socketPath] = self::_getRedisConnection($socketPath);
		}
		return self::$_specific_connections[$socketPath];
	}

	/**
	 * @param string $socketPath
	 *
	 * @return Redis
	 * @throws RedisException
	 */
	protected static function _getRedisConnection($socketPath)
	{
		$redis = new Redis();
		$redis->connect($socketPath);
		if($redis->ping() !== 'PONG')
		{
			if(!$redis->auth('9718B6A8-138C-4C21-9D72-FC6703C62AC0'))
			{
				$lastError = $redis->getLastError();
				if($lastError !== 'ERR Client sent AUTH, but no password is set')
				{
					throw new RedisException($lastError);
				}
				$redis->clearLastError();
			}
		}
		return $redis;
	}
}