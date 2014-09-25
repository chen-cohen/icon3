<?php

namespace LPO\Persistence;

use Db\Adapter\Pdo\Dbase;

class PublisherType extends CRUD {

	/**
	 * @var string
	 */
	const TABLE_NAME = 'lpo_db_ORM.publisher_type';
	/**
	 * @var int
	 */
	protected $_id;
	/**
	 * @var string
	 */
	protected $_name;

	/**
	 * @param array $data
	 */
	protected function __construct(array $data)
	{
		$this->_id       = (int)$data['id'];
		$this->_name     = (string)$data['name'];
	}

	/**
	 * @param array $data
	 *
	 * @param bool $lazy
	 *
	 * @return PublisherType
	 */
	public static function factory($data,$lazy = false)
	{
		/**
		 * @var PublisherType $instance
		 */
		$instance = parent::factory($data);
		return $instance;
	}

	/**
	 * @return mixed
	 */
	public function create()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('INSERT INTO '.static::TABLE_NAME.'(`id`,`name`) VALUES (:id,:name)');

		$stmt->bindValue(':id', $this->_id);
		$stmt->bindValue(':name', $this->_name);

		return $stmt->execute();
	}

	/**
	 * @return mixed
	 */
	public function read()
	{
		// TODO: Implement read() method.
	}

	/**
	 * @return mixed
	 */
	public function update()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('UPDATE '.static::TABLE_NAME.' SET `name`=:name WHERE `id` = :id');

		$stmt->bindValue(':id', $this->_id);
		$stmt->bindValue(':name', $this->_name);

		return $stmt->execute();
	}

	/**
	 * @return mixed
	 */
	public function delete()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('DELETE FROM '.static::TABLE_NAME.' WHERE `id` = :id');

		$stmt->bindValue(':id', $this->_id);
		return $stmt->execute();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->_id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName($name)
	{
		$this->_name = $name;
	}
}