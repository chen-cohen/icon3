<?php

namespace LPO\Persistence;

use Db\Adapter\Pdo\Dbase;

class Configuration extends CRUD {

	const KEY_DOWNLOAD_DOMAIN                  = 'download_domain';
	const KEY_CORRUPT_DOWNLOAD_DOMAIN          = 'corrupt_download_domain';
	const KEY_IFRAME_LINK_CHROME               = 'iframe_link_chrome';
	const KEY_DOWNLOAD_DOMAIN_MSIE             = 'download_domain_msie';
	const KEY_MAC_OSX_REDIRECT_URL             = 'generic_mac_osx_redirect_url';
	const KEY_MOBILE_REDIRECT_URL              = 'mobile_redirect_url';
	const KEY_VAUDIX_MAC_OSX_REDIRECT_URL      = 'vaudix_mac_osx_redirect_url';
	const KEY_VIDEO_MAC_OSX_REDIRECT_URL       = 'video_mac_osx_redirect_url';
	const KEY_MAC_OSX_TORRENT_DOWNLOAD_DOMAIN  = 'mac_osx_torrent_download_domain';
	const KEY_FILE_SAVE_DOWNLOAD_DOMAIN        = 'filesave_download_domain';

	const TABLE_NAME = 'lpo_db_ORM.configuration';

	protected static $_keys = array
	(
		self::KEY_DOWNLOAD_DOMAIN => 'Download Domain',
		self::KEY_CORRUPT_DOWNLOAD_DOMAIN => 'Corrupt Download Domain',
		self::KEY_IFRAME_LINK_CHROME => 'Chrome iframe domain',
		self::KEY_DOWNLOAD_DOMAIN_MSIE => 'Download domain (IE)',
		self::KEY_MAC_OSX_REDIRECT_URL => 'MacOSX redirect url',
		self::KEY_MOBILE_REDIRECT_URL => 'Mobile redirect URL',
		self::KEY_VAUDIX_MAC_OSX_REDIRECT_URL => 'Vaudix MacOSX redirect domain',
		self::KEY_VIDEO_MAC_OSX_REDIRECT_URL => 'VIDEO MacOSX redirect url',
		self::KEY_MAC_OSX_TORRENT_DOWNLOAD_DOMAIN => 'TORRENT MacOSX download domain',
		self::KEY_FILE_SAVE_DOWNLOAD_DOMAIN => 'FileSave download domain'
	);
	/**
	 * @var string
	 */
	protected $_key;
	/**
	 * @var string
	 */
	protected $_value;
	/**
	 * @var string
	 */
	protected $_description;
	/**
	 * @var int
	 */
	protected $_publisher_id;

	/**
	 * @var
	 */
	protected $_updated_at;

	/**
	 * @param array $data
	 */
	protected function __construct(array $data)
	{
		$this->_key          = (string)$data['key'];
		$this->_value        = (string)$data['value'];
		$this->_description  = (string)$data['description'];
		$this->_updated_at	 = (string)$data['updated_at'];
		if(isset($data['publisher_id'])) { $this->_publisher_id = (int)$data['publisher_id']; }

	}

	/**
	 * @return static[]
	 */
	public static function loadAllGlobals()
	{
		$adapter = Dbase::getConnection();

		$stmt = $adapter->query('SELECT * FROM ' . static::TABLE_NAME . ' WHERE `publisher_id` IS NULL');
		$stmt-> execute();
		$data = $stmt->fetchAll($adapter::FETCH_ASSOC);
		$initArray = static::buildKeysArray(null);
		$resultArray = static::mergeDbConsArrays($data,$initArray);
		$obj = [];
		foreach($resultArray as $key => $value){
			$obj[$value['key']] = static::factory($value);
		}
		return $obj;
	}

	/**
	 * @param $publisherId
	 * @return array[]
	 */
	public static function loadAllKeysValuesByPublisher($publisherId)
	{
		$adapter = Dbase::getConnection();
		$PDOStatement = $adapter->prepare('SELECT * FROM ' . static::TABLE_NAME . ' WHERE `publisher_id` = :publisher_id');
		$PDOStatement->bindValue(':publisher_id', $publisherId);
		$PDOStatement->execute();
		$data = $PDOStatement->fetchAll($adapter::FETCH_ASSOC);
		$initArray = static::buildKeysArray($publisherId);
		$resultArray = static::mergeDbConsArrays($data,$initArray);
		return $resultArray;
	}

	/**
	 * @param $publisherId
	 * @return array[]
	 */
	public static function buildKeysArray($publisherId){
		$resultArray = [];
		foreach (Configuration::getKeysWithDescription() as $key => $desc){
			$resultArray[$key] = array(
				'key'=> $key ,
				'value' => '',
				'description' => $desc,
				'publisher_id' => $publisherId,
				'updated_at' => ''
			);
		}
		return $resultArray;
	}

	/**
	 * @param $data
	 * @param $initArray
	 * @return array[]
	 */
	public static function mergeDbConsArrays($data,$initArray){
		foreach($data as $element)
		{
			$initArray[$element['key']]['value'] = $element['value'];
			$initArray[$element['key']]['updated_at'] = $element['updated_at'];

		}
		return array_values($initArray);
	}

	/**
	 * @param string $key
	 * @param int[] $publisherIds
	 * @return int
	 */
	public static function deleteMultipleByKeyAndPublisher($key, $publisherIds)
	{
		return Dbase::getConnection()->exec('DELETE FROM '.static::TABLE_NAME.' WHERE `key`="'.$key.'" AND `publisher_id` IN ('.implode(',',$publisherIds).')');
	}

	/**
	 * @param string $key
	 * @param string|int|float $publisherId
	 *
	 * @return static
	 */
	public static function loadByKeyAndPublisher($key, $publisherId)
	{
		$adapter = Dbase::getConnection();
		if(is_null($publisherId))
		{
			$PDOStatement = $adapter->prepare('SELECT * FROM ' . static::TABLE_NAME . ' WHERE `key` = :key AND `publisher_id` IS NULL');
			$PDOStatement->bindValue(':key', $key);
		}
		else
		{
			$PDOStatement = $adapter->prepare('SELECT * FROM ' . static::TABLE_NAME . ' WHERE `key` = :key AND `publisher_id` = :publisher_id');
			$PDOStatement->bindValue(':key', $key);
			$PDOStatement->bindValue(':publisher_id', $publisherId);
		}
		$PDOStatement->execute();

		$data = $PDOStatement->fetch($adapter::FETCH_ASSOC);
		if(!$data)
		{
			return null;
		}

		return static::factory($data);
	}
	/**
	 * @return mixed
	 */
	public function create()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('INSERT INTO '.static::TABLE_NAME.' SET `key` = :key, `value` = :value , `description` = :description , `hash` = :hash ');

		$stmt->bindValue(':key', $this->_key);
		$stmt->bindValue(':value', $this->_value);
		$stmt->bindValue(':description', $this->_description);
		$stmt->bindValue(':hash', md5($this->_publisher_id.'_'.$this->_key));
		return $stmt->execute();
	}

	/**
	 * @return mixed
	 */
	public function read()
	{
		$adapter = Dbase::getConnection();

		if(is_null($this->_publisher_id))
		{
			$stmt = $adapter->prepare('SELECT * FROM ' . static::TABLE_NAME . ' WHERE `key` = :key AND publisher_id IS NULL');
			$stmt->bindValue(':key', $this->_key);
		}
		else
		{
			$stmt = $adapter->prepare('SELECT * FROM ' . static::TABLE_NAME . ' WHERE `key` = :key AND publisher_id = :publisher_id');
			$stmt->bindValue(':key', $this->_key);
			$stmt->bindValue(':publisher_id', $this->_publisher_id);
		}

		$stmt->execute();
		return $stmt->fetchColumn(0);
	}

	/**
	 * @return mixed
	 */
	public function update()
	{
		$adapter = Dbase::getConnection();

		$stmt = $adapter->prepare('REPLACE INTO '.static::TABLE_NAME.' SET value = :value, description = :description , `key` = :key , publisher_id = :publisher_id , hash = :hash');
		$stmt->bindValue(':key', $this->_key);
		$stmt->bindValue(':value', $this->_value);
		$stmt->bindValue(':description', $this->_description);
		$stmt->bindValue(':publisher_id', $this->_publisher_id);
		$stmt->bindValue(':hash', md5($this->_publisher_id.'_'.$this->_key));


		return $stmt->execute();
	}

	/**
	 * @return mixed
	 */
	public function delete()
	{
		$adapter = Dbase::getConnection();

		if(is_null($this->_publisher_id))
		{
			$stmt = $adapter->prepare('DELETE FROM ' . static::TABLE_NAME . ' WHERE `key` = :key AND publisher_id IS NULL');
			$stmt->bindValue(':key', $this->_key);
		}
		else
		{
			$stmt = $adapter->prepare('DELETE FROM ' . static::TABLE_NAME . ' WHERE `key` = :key AND publisher_id = :publisher_id');
			$stmt->bindValue(':key', $this->_key);
			$stmt->bindValue(':publisher_id', $this->_publisher_id);
		}

		return $stmt->execute();
	}

	/**
	 * @return array
	 */
	public static function getKeysWithDescription()
	{
		return static::$_keys;
	}

	/**
	 * @param string $key
	 * @return string|null
	 */
	public static function getKeyDescription($key)
	{
		return isset(static::$_keys[$key]) ? static::$_keys[$key] : null;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->_value = $value;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * @param string $key
	 */
	public function setKey($key)
	{
		$this->_key = $key;
	}

	/**
	 * @return string
	 */
	public function getKey()
	{
		return $this->_key;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->_description = $description;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->_description;
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
	 * @return mixed
	 */
	public function getUpdatedAt()
	{
		return $this->_updated_at;
	}
}