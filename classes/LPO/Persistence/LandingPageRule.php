<?php

namespace LPO\Persistence;

use Db\Adapter\Pdo\Dbase;

class LandingPageRule extends CRUD {

	/**
	 * @var string
	 */
	const TABLE_NAME = 'lpo_db_ORM.landing_page_rule';
	/**
	 * @var int
	 */
	protected $_landing_page_id;
	/**
	 * @var int
	 */
	protected $_rule_id;
	/**
	 * @var int
	 */
	protected $_value;

	/**
	 * @param array $data
	 */
	protected function __construct(array $data)
	{
		$this->_landing_page_id = (int)$data['landing_page_id'];
		$this->_rule_id         = (int)$data['rule_id'];
		$this->_value           = (int)$data['value'];

	}

	/**
	 * @return mixed
	 */
	public function create()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('INSERT INTO '.static::TABLE_NAME.' SET `landing_page_id` = :landing_page_id, `rule_id` = :rule_id , `value` = :value');

		$stmt->bindValue(':landing_page_id', $this->_landing_page_id);
		$stmt->bindValue(':rule_id', $this->_rule_id);
		$stmt->bindValue(':value', $this->_value);

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
		$stmt    = $adapter->prepare('UPDATE '.static::TABLE_NAME.' SET `value` = :value WHERE `landing_page_id` = :landing_page_id AND `rule_id` = :rule_id');

		$stmt->bindValue(':landing_page_id', $this->_landing_page_id,$adapter::PARAM_INT);
		$stmt->bindValue(':rule_id', $this->_rule_id,$adapter::PARAM_INT);
		$stmt->bindValue(':value', $this->_value,$adapter::PARAM_INT);

		return $stmt->execute();
	}

	/**
	 * @return mixed
	 */
	public function delete()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('DELETE FROM '.static::TABLE_NAME.' WHERE `landing_page_id` = :landing_page_id AND `rule_id` = :rule_id');

		$stmt->bindValue(':landing_page_id', $this->_landing_page_id,$adapter::PARAM_INT);
		$stmt->bindValue(':rule_id', $this->_rule_id,$adapter::PARAM_INT);

		return $stmt->execute();
	}

	/**
	 * @param int $landingPageId
	 *
	 * @return bool
	 */
	public static function deleteAllByLandingPageId($landingPageId)
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('DELETE FROM '.static::TABLE_NAME.' WHERE `landing_page_id` = :landing_page_id');
		$stmt->bindValue(':landing_page_id', (int)$landingPageId, $adapter::PARAM_INT);
		return $stmt->execute();
	}
	/**
	 * @param int $ruleId
	 *
	 * @return bool
	 */
	public static function deleteAllByRuleId($ruleId)
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('DELETE FROM '.static::TABLE_NAME.' WHERE `rule_id` = :rule_id');
		$stmt->bindValue(':rule_id', (int)$ruleId, $adapter::PARAM_INT);
		return $stmt->execute();
	}

	/**
	 * @param int $landing_page_id
	 */
	public function setLandingPageId($landing_page_id)
	{
		$this->_landing_page_id = $landing_page_id;
	}

	/**
	 * @return int
	 */
	public function getLandingPageId()
	{
		return $this->_landing_page_id;
	}

	/**
	 * @param int $rule_id
	 */
	public function setRuleId($rule_id)
	{
		$this->_rule_id = $rule_id;
	}

	/**
	 * @return int
	 */
	public function getRuleId()
	{
		return $this->_rule_id;
	}

	/**
	 * @param int $value
	 */
	public function setValue($value)
	{
		$this->_value = $value;
	}

	/**
	 * @return int
	 */
	public function getValue()
	{
		return $this->_value;
	}

}