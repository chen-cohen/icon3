<?php

namespace LPO\Persistence;

use Db\Adapter\Pdo\Dbase;

class ExpiredDomain extends CRUD {

	const TABLE_NAME = 'lpo_db_ORM.expired_domain';

	/**
	 * @var string
	 */
	protected $_origin;
	/**
	 * @var string
	 */
	protected $_target;

	/**
	 * @param array $data
	 */
	protected function __construct(array $data)
	{
		$this->_origin = (string)$data['origin'];
		$this->_target = (string)$data['target'];
	}

	/**
	 * @return mixed
	 */
	public function create()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('INSERT INTO '.static::TABLE_NAME.' SET origin = :origin, target = :target ');

		$stmt->bindValue(':origin', $this->_origin);
		$stmt->bindValue(':target', $this->_target);
		return $stmt->execute();
	}

	/**
	 * @return mixed
	 */
	public function read()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('SELECT target FROM '.static::TABLE_NAME.' WHERE origin = :origin LIMIT 1');

		$stmt->bindValue(':origin', $this->_origin);
		$stmt->execute();
		return $stmt->fetchColumn(0);
	}

	/**
	 * @return mixed
	 */
	public function update()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('UPDATE '.static::TABLE_NAME.' SET target = :target WHERE origin = :origin');

		$stmt->bindValue(':origin', $this->_origin);
		$stmt->bindValue(':target', $this->_target);
		return $stmt->execute();
	}

	/**
	 * @return mixed
	 */
	public function delete()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('DELETE FROM '.static::TABLE_NAME.' WHERE origin = :origin');

		$stmt->bindValue(':origin', $this->_origin);
		return $stmt->execute();
	}

	/**
	 * @param string $target
	 */
	public function setTarget($target)
	{
		$this->_target = $target;
	}

	/**
	 * @return string
	 */
	public function getTarget()
	{
		return $this->_target;
	}

	/**
	 * @param string $origin
	 */
	public function setOrigin($origin)
	{
		$this->_origin = $origin;
	}

	/**
	 * @return string
	 */
	public function getOrigin()
	{
		return $this->_origin;
	}

}