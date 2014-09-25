<?php

namespace Db\Adapter\Pdo;

use Db\Adapter\Pdo as Adapter_Pdo;
use PDO;
use PDOException;
use ErrorException;

class Dbase implements Adapter_Pdo {

	const MAX_QUERY_SIZE_IN_BYTES = 1048576;
	/**
	 * @var PDO
	 */
	protected static $_connection;

	/**
	 *
	 */
	protected function __construct()
	{
		$DB_USER = 'admin';
		$DB_PASS = 'Go512625';
		try
		{
			static::$_connection = new PDO('mysql:host=dbase.webpick.net;dbname=lpo_db;', $DB_USER, $DB_PASS, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		} catch (PDOException $e)
		{
			throw new ErrorException(str_replace(array($DB_USER, $DB_PASS), array('username', 'password'), $e->getMessage()));
		}
	}

	/**
	 * @return int
	 */
	public function getMaxQuerySize()
	{
		return self::MAX_QUERY_SIZE_IN_BYTES;
	}

	/**
	 * @return PDO
	 */
	public static function getConnection()
	{
		if (!isset(self::$_connection))
		{
			new self();
		}
		return self::$_connection;
	}
}