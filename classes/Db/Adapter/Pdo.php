<?php

namespace Db\Adapter;

interface Pdo {

	/**
	 * @return \PDO
	 */
	static function getConnection();

	/**
	 * @return int
	 */
	public function getMaxQuerySize();
}