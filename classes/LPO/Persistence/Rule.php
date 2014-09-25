<?php

namespace LPO\Persistence;

use Db\Adapter\Pdo\Dbase;

/**
 * @method static \LPO\Persistence\Rule loadBy()

 * @method static \LPO\Persistence\Rule[] loadMultipleBy()
 */
class Rule extends CRUD {

	/**
	 * @var string
	 */
	const TABLE_NAME = 'lpo_db_ORM.rule';
	/**
	 * @var LandingPage[]
	 */
	protected $_landing_pages;
	/**
	 * @var Publisher
	 */
	protected $_publisher;
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
	protected $_priority;
	/**
	 * @var int
	 */
	protected $_user_defined = true;
	/**
	 * @var int
	 */
	protected $_auto_increment_id;

	/**
	 * @param array $data
	 */
	function __construct(array $data)
	{
		$this->_os_id        = (int)$data['os_id'];
		$this->_priority     = (int)$data['priority'];
		$this->_browser_id   = (int)$data['browser_id'];
		$this->_publisher_id = (int)$data['publisher_id'];
		$this->_country_code = (string)$data['country_code'];
		$this->_language     = (string)$data['language'];

		if(isset($data['id'])){$this->_auto_increment_id = (int)$data['id'];}
		if(isset($data['user_defined'])){$this->_user_defined = (bool)$data['user_defined'];}
	}

	/**
	 * @param array $data
	 *
	 * @param bool $lazy
	 *
	 * @return Rule
	 */
	public static function factory($data,$lazy = false)
	{
		/**
		 * @var Rule $instance
		 */
		$instance = parent::factory($data);
		if($lazy) {return $instance;}
		$instance->_loadPublisher();
		return $instance;
	}

	/**
	 * @return int - returns the newly created rule id
	 */
	public function create()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('INSERT INTO '.static::TABLE_NAME.' (`user_defined`,`priority`,`publisher_id`,`browser_id`,`os_id`,`language`,`country_code`)	VALUES (:user_defined,:priority,:publisher_id,:browser_id,:os_id,:language,:country_code)');

		$stmt->bindValue(':os_id', $this->_os_id);
		$stmt->bindValue(':priority', $this->_priority);
		$stmt->bindValue(':language', $this->_language);
		$stmt->bindValue(':browser_id', $this->_browser_id);
		$stmt->bindValue(':user_defined', $this->_user_defined ? '1' : '0');
		$stmt->bindValue(':publisher_id', $this->_publisher_id);
		$stmt->bindValue(':country_code', $this->_country_code);

		$stmt->execute();
		$lastInsertId = $adapter->lastInsertId();
		$this->_auto_increment_id = $lastInsertId;

		return $lastInsertId;
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

		$this->_auto_increment_id = $data['id'];
		$this->_priority          = $data['priority'];

		return $data;
	}

	/**
	 * @return mixed
	 */
	public function update()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('UPDATE '.static::TABLE_NAME.' SET user_defined = :user_defined,priority = :priority WHERE `id` = :id');

		$stmt->bindValue(':id', $this->_auto_increment_id);
		$stmt->bindValue(':user_defined', ($this->_user_defined ? '1' : '0'));
		$stmt->bindValue(':priority', $this->_priority);

		return $stmt->execute();
	}

	/**
	 * @return mixed
	 */
	public function delete()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('DELETE FROM '.static::TABLE_NAME.' WHERE `id` = :id');

		$stmt->bindValue(':id', $this->_auto_increment_id,$adapter::PARAM_INT);

		return $stmt->execute();
	}

	/**
	 * @return int
	 */
	public function getPublisherId()
	{
		return $this->_publisher_id;
	}

	/**
	 * @param int $publisherId
	 */
	public function setPublisherId($publisherId)
	{
		$this->_publisher_id = $publisherId;
	}

	/**
	 * @return int
	 */
	public function getAutoIncrementId()
	{
		return $this->_auto_increment_id;
	}

	/**
	 * @return int
	 */
	public function getPriority()
	{
		return $this->_priority;
	}

	/**
	 * @param int $priority
	 */
	public function setPriority($priority)
	{
		$this->_priority = $priority;
	}

	/**
	 * @return int
	 */
	public function getBrowserId()
	{
		return $this->_browser_id;
	}

	/**
	 * @param int $browser_id
	 */
	public function setBrowserId($browser_id)
	{
		$this->_browser_id = $browser_id;
	}

	/**
	 * @return string
	 */
	public function getCountryCode()
	{
		return $this->_country_code;
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
	public function getLanguage()
	{
		return $this->_language;
	}

	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		$this->_language = $language;
	}

	/**
	 * @return int
	 */
	public function getOsId()
	{
		return $this->_os_id;
	}

	/**
	 * @param int $os_id
	 */
	public function setOsId($os_id)
	{
		$this->_os_id = $os_id;
	}

	/**
	 * @return Publisher
	 */
	public function getPublisher()
	{
		return $this->_publisher;
	}

	/**
	 * @param Publisher $publisher
	 */
	public function setPublisher(Publisher $publisher)
	{
		$this->_publisher = $publisher;
	}

	/**
	 * @return bool
	 */
	public function isUserDefined()
	{
		return $this->_user_defined;
	}

	/**
	 * @return LandingPage[]
	 */
	public function getRelatedLandingPages()
	{
		if(is_null($this->_landing_pages))
		{
			$this->_landing_pages = $this->fetchManyToManyRelation('landing_page_rule', 'LPO\Persistence\LandingPage', 'rule_id', $this->getAutoIncrementId(), 'landing_page_id');
		}

		return $this->_landing_pages;
	}

	/**
	 * @return  LandingPageRule[]
	 */
	public function getRelatedLandingPageRules()
	{
		return $this->fetchOneToManyRelation('LPO\Persistence\LandingPageRule',$this->getAutoIncrementId(),'rule_id');
	}

	/**
	 * @return bool
	 */
	public function exists()
	{
		return !is_null($this->read());
	}

	/**
	 *
	 */
	protected function _loadPublisher()
	{
		$this->_publisher = Publisher::loadBy('id', $this->_publisher_id);
	}

	/**
	 * @param int[] $ids
	 * @param bool $lazy
	 * @return LandingPageRule[]
	 */
	public static function getLandingPageRulesByIds(array $ids,$lazy = false)
	{
		$adapter = Dbase::getConnection();

		$query = $adapter->query('SELECT * FROM '.LandingPageRule::TABLE_NAME.' WHERE rule_id IN ('.implode(',',$ids).')');
		$dataSet = array();

		while($row = $query->fetch(\PDO::FETCH_ASSOC))
		{
			$dataSet[] = $row;
		}

		if(empty($dataSet))
		{
			return array();
		}

		$collection = array();
		foreach($dataSet as $element)
		{
			$collection[] = LandingPageRule::factory($element,$lazy);
		}

		return $collection;
	}
}