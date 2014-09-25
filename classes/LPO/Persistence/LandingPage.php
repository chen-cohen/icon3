<?php

namespace LPO\Persistence;

use Db\Adapter\Pdo\Dbase;
use ErrorException;

class LandingPage extends CRUD {

	/**
	 * @var string
	 */
	const TABLE_NAME = 'lpo_db_ORM.landing_page';
	/**
	 * @var string
	 */
	protected $_id;
	/**
	 * @var string
	 */
	protected $_created_at;
	/**
	 * @var string
	 */
	protected $_updated_at;
	/**
	 * @var Rule[]
	 */
	protected $_rules;

	/**
	 * @param array $data
	 */
	protected function __construct(array $data)
	{
		$this->_id         = (string)$data['id'];

	}

	/**
	 * @param array $data
	 *
	 * @param bool $lazy
	 *
	 * @return LandingPage
	 */
	public static function factory($data,$lazy = false)
	{
		/**
		 * @var LandingPage $instance
		 */
		$instance = parent::factory($data);
		if(isset($data['created_at'])) $instance->_created_at = (string)$data['created_at'];
		if(isset($data['updated_at'])) $instance->_updated_at = (string)$data['updated_at'];
		return $instance;
	}

	/**
	 * @return mixed
	 */
	public function create()
	{
		$this->_created_at = $this->_updated_at = date('Y-m-d H:i:s');

		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('INSERT INTO '.static::TABLE_NAME.' SET `id` = :id, created_at = :created_at , updated_at = :updated_at');

		$stmt->bindValue(':id', $this->_id);
		$stmt->bindValue(':created_at', $this->_created_at);
		$stmt->bindValue(':updated_at', $this->_updated_at);

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
		$this->_updated_at = date('Y-m-d H:i:s');

		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('UPDATE '.static::TABLE_NAME.' SET updated_at = :updated_at WHERE `id` = :id');

		$stmt->bindValue(':id', $this->_id);
		$stmt->bindValue(':updated_at', $this->_updated_at);

		return $stmt->execute();
	}

	/**
	 * @return bool|mixed
	 * @throws ErrorException
	 */
	public function delete()
	{
//		$adapter = Dbase::getConnection();
//		$stmt    = $adapter->prepare('DELETE FROM '.static::TABLE_NAME.' WHERE `id` = :id');
//
//		$stmt->bindValue(':id', $this->_id);
//		return $stmt->execute();
	}

	/**
	 * @param string $created_at
	 */
	public function setCreatedAt($created_at)
	{
		$this->_created_at = $created_at;
	}

	/**
	 * @return string
	 */
	public function getCreatedAt()
	{
		return $this->_created_at;
	}

	/**
	 * @param string $id
	 */
	public function setId($id)
	{
		$this->_id = $id;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param string $updated_at
	 */
	public function setUpdatedAt($updated_at)
	{
		$this->_updated_at = $updated_at;
	}

	/**
	 * @return string
	 */
	public function getUpdatedAt()
	{
		return $this->_updated_at;
	}

	/**
	 * @return Rule[]
	 */
	public function getRelatedRules()
	{
		if(is_null($this->_rules))
		{
			$this->_rules = $this->fetchManyToManyRelation('landing_page_rule','LPO\Persistence\Rule','landing_page_id',$this->_id,'rule_id');
		}

		return $this->_rules;
	}
}