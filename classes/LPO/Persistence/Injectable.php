<?php

namespace LPO\Persistence;

use Db\Adapter\Pdo\Dbase;


class Injectable extends CRUD {

	/**
	 * @var string
	 */
	const TABLE_NAME = 'lpo_db_ORM.injectable_setting';
	/**
	 * @var int
	 */
	protected $_publisher_id;
	/**
	 * @var string
	 */
	protected $_country_code;
	/**
	 * @var string
	 */
	protected $_language;
	/**
	 * @var int
	 */
	protected $_os_id;
	/**
	 * @var int
	 */
	protected $_browser_id;
	/**
	 * @var int
	 */
	protected $_value;

	/**
	 * @param array $data
	 */
	function __construct(array $data)
	{
		$this->_os_id        = (int)$data['os_id'];
		$this->_value         = (int)$data['value'];
		$this->_browser_id   = (int)$data['browser_id'];
		$this->_publisher_id = (int)$data['publisher_id'];
		$this->_country_code = (string)$data['country_code'];
		$this->_language     = (string)$data['language'];
	}

	/**
	 * @return int
	 */
	public function create()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('INSERT INTO '.static::TABLE_NAME.' (`value`,`publisher_id`,`browser_id`,`os_id`,`language`,`country_code`)	VALUES (:value,:publisher_id,:browser_id,:os_id,:language,:country_code)');

		$stmt->bindValue(':os_id', $this->_os_id);
		$stmt->bindValue(':value', $this->_value);
		$stmt->bindValue(':language', $this->_language);
		$stmt->bindValue(':browser_id', $this->_browser_id);
		$stmt->bindValue(':publisher_id', $this->_publisher_id);
		$stmt->bindValue(':country_code', $this->_country_code);

		return $stmt->execute();
	}

	/**
	 * @return mixed
	 */
	public function read()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('SELECT * FROM '.static::TABLE_NAME.' WHERE `publisher_id` = :publisher_id AND `browser_id` = :browser_id AND `os_id` = :os_id AND `language` = :language AND `country_code` = :country_code');

		$stmt->bindValue(':os_id', $this->_os_id);
		$stmt->bindValue(':language', $this->_language);
		$stmt->bindValue(':browser_id', $this->_browser_id);
		$stmt->bindValue(':publisher_id', $this->_publisher_id);
		$stmt->bindValue(':country_code', $this->_country_code);

		$stmt->execute();
		$data = $stmt->fetch($adapter::FETCH_ASSOC);

		if(!$data)
		{
			return null;
		}
		$this->_value = $data['value'];
		return $data;
	}

	/**
	 * @return mixed
	 */
	public function update()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('UPDATE '.static::TABLE_NAME.' SET `value` = :value WHERE `publisher_id` = :publisher_id AND `browser_id` = :browser_id AND `os_id` = :os_id AND `language` = :language AND `country_code` = :country_code');

		$stmt->bindValue(':value', $this->_value);
		$stmt->bindValue(':os_id', $this->_os_id);
		$stmt->bindValue(':language', $this->_language);
		$stmt->bindValue(':browser_id', $this->_browser_id);
		$stmt->bindValue(':publisher_id', $this->_publisher_id);
		$stmt->bindValue(':country_code', $this->_country_code);

		return $stmt->execute();
	}

	/**
	 * @return mixed
	 */
	public function delete()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('DELETE FROM '.static::TABLE_NAME.' WHERE `publisher_id` = :publisher_id AND `browser_id` = :browser_id AND `os_id` = :os_id AND `language` = :language AND `country_code` = :country_code');

		$stmt->bindValue(':os_id', $this->_os_id);
		$stmt->bindValue(':language', $this->_language);
		$stmt->bindValue(':browser_id', $this->_browser_id);
		$stmt->bindValue(':publisher_id', $this->_publisher_id);
		$stmt->bindValue(':country_code', $this->_country_code);

		return $stmt->execute();
	}

	/**
	 * @return bool
	 */
	public function exists()
	{
		return !is_null($this->read());
	}

	/**
	 * @param int $browser_id
	 */
	public function setBrowserId($browser_id)
	{
		$this->_browser_id = $browser_id;
	}

	/**
	 * @return int
	 */
	public function getBrowserId()
	{
		return $this->_browser_id;
	}

	/**
	 * @param string $country_code
	 */
	public function setCountryCode($country_code)
	{
		$this->_country_code = $country_code;
	}

	/**
	 * @return string
	 */
	public function getCountryCode()
	{
		return $this->_country_code;
	}

	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		$this->_language = $language;
	}

	/**
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->_language;
	}

	/**
	 * @param int $os_id
	 */
	public function setOsId($os_id)
	{
		$this->_os_id = $os_id;
	}

	/**
	 * @return int
	 */
	public function getOsId()
	{
		return $this->_os_id;
	}

	/**
	 * @param int $publisher_id
	 */
	public function setPublisherId($publisher_id)
	{
		$this->_publisher_id = $publisher_id;
	}

	/**
	 * @return int
	 */
	public function getPublisherId()
	{
		return $this->_publisher_id;
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