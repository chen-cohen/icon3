<?php

namespace LPO\Persistence;

use Db\Adapter\Pdo\Dbase;
use Request;

class DownloadMethodRule extends CRUD {

	/**
	 * @var string
	 */
	const TABLE_NAME = 'lpo_db_ORM.download_method_rule';
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
	protected $_priority;
	/**
	 * @var int
	 */
	protected $_publisher_id = \LPO\Publisher::DEFAULT_ID;
	/**
	 * @var int
	 */
	protected $_download_method_id = DownloadMethod::__default;
	/**
	 * @var string
	 */
	protected $_country_code = Request::COUNTRY_CODE_DEFAULT;
	/**
	 * @var int
	 */
	protected $_browser_id = Request::BROWSER_DEFAULT;

	/**
	 * @param array $data
	 */
	function __construct(array $data)
	{
		$this->_priority           = (int)$data['priority'];
		$this->_browser_id         = (int)$data['browser_id'];
		$this->_publisher_id       = (int)$data['publisher_id'];
		$this->_download_method_id = (int)$data['download_method_id'];
		$this->_country_code       = (string)$data['country_code'];
	}

	/**
	 * @param array $data
	 *
	 * @param bool $lazy
	 *
	 * @return DownloadMethodRule
	 */
	public static function factory($data,$lazy = false)
	{
		/**
		 * @var DownloadMethodRule $instance
		 */
		$instance = parent::factory($data,$lazy);
		if($lazy) {return $instance;}
		$instance->_loadPublisher();
		return $instance;
	}

	/**
	 * @return mixed|void
	 */
	public function create()
	{
		// TODO: Implement create() method.
	}

	/**
	 * @return mixed
	 */
	public function read()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('SELECT * FROM '.static::TABLE_NAME.' WHERE `publisher_id` = :publisher_id AND `browser_id` = :browser_id AND `country_code` = :country_code');

		$stmt->bindValue(':browser_id', $this->_browser_id);
		$stmt->bindValue(':publisher_id', $this->_publisher_id);
		$stmt->bindValue(':country_code', $this->_country_code);

		$stmt->execute();
		$data = $stmt->fetch($adapter::FETCH_ASSOC);

		if(!$data)
		{
			return null;
		}

		$this->_download_method_id = $data['download_method_id'];
		$this->_priority           = $data['priority'];

		return $data;
	}

	/**
	 * @return mixed
	 */
	public function update()
	{
		// TODO: Implement update() method.
	}

	/**
	 * @return mixed
	 */
	public function delete()
	{
		// TODO: Implement delete() method.
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
	 * @param int $download_method_id
	 */
	public function setDownloadMethodId($download_method_id)
	{
		$this->_download_method_id = $download_method_id;
	}

	/**
	 * @return int
	 */
	public function getDownloadMethodId()
	{
		return $this->_download_method_id;
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
	public function getPriority()
	{
		return $this->_priority;
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
	 *
	 */
	protected function _loadPublisher()
	{
		$this->_publisher = Publisher::loadBy('id', $this->_publisher_id);
	}
}