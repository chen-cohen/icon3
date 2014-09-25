<?php

namespace Db\Adapter\Pdo;

use Db\Adapter\Pdo as Adapter_Pdo;
use PDO;
use PDOException;

class Redshift implements Adapter_Pdo {

	const MAX_QUERY_SIZE_IN_BYTES = 16777216;
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
			static::$_connection = new PDO('pgsql:host=webpick-redshift.c8kkloutxb04.us-west-2.redshift.amazonaws.com;port=5439;dbname=dw;', $DB_USER, $DB_PASS, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		} catch (PDOException $e)
		{
			die(str_replace(array($DB_USER, $DB_PASS), array('username', 'password'), $e->getMessage()));
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