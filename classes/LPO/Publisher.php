<?php


namespace LPO;

use ErrorException;
use LPO\Db\Redis\Buffer;

class Publisher {

	const REDIS_KEY          = 'lpo_publisher';
	const REDIS_KEY_PATH_MAP = 'lpo_publisher_path_map';

	const META_DATA_KEY_TYPE             = 'type';
	const META_DATA_KEY_UA               = 'ua';
	const META_DATA_KEY_NAME             = 'name';
	const META_DATA_KEY_PATH             = 'path';
	const META_DATA_KEY_ALIAS            = 'alias';
	const META_DATA_KEY_BRAND_ID         = 'brandId';
	const META_DATA_KEY_DOWNLOAD_DOMAIN  = 'downloadDomain';
	const META_DATA_KEY_LANDING_PAGE_IDS = 'landingPageIds';

	const DEFAULT_ID = 0;

//	const ENCRYPTION_LOOKUP_TABLE = 'f93tTU4WrbIhzFuCHDmlMP5OVcogeijKZk-E78SvQRLnqXJwdN0A6_21BYsyaxGp';

	/**
	 * @var bool
	 */
	protected $_is_loaded = false;
	/**
	 * @var int
	 */
	protected $_id;
	/**
	 * @var int
	 */
	protected $_type;
	/**
	 * @var string
	 */
	protected $_ua;
	/**
	 * @var string
	 */
	protected $_name;
	/**
	 * @var string
	 */
	protected $_path;
	/**
	 * @var string
	 */
	protected $_alias;
	/**
	 * @var Brand
	 */
	protected $_brand;
	/**
	 * @var string
	 */
	protected $_download_domain;
	/**
	 * @var string[]
	 */
	protected $_landing_page_ids;

	/**
	 * @param int $id
	 */
	public function __construct($id)
	{
		$this->_id = $id;
	}

	/**
	 * @param string $path
	 * @param bool $populate
	 *
	 * @return Publisher
	 * @throws ErrorException
	 */
	public static function loadByPath($path, $populate = true)
	{
		$path   = trim($path);
		if(empty($path))
		{
			//todo: add alert
			throw new ErrorException('Provided path/alias is not linked to a publisher!');
		}

		$buffer = Buffer::getConnection();
		$id     = $buffer->hGet(self::REDIS_KEY_PATH_MAP, mb_strtolower($path));
		if(!$id && !is_numeric($id))
		{
			//todo: add alert
			throw new ErrorException('Provided path/alias is not linked to a publisher!');
		}
		$publisher = new self((int)$id);
		if($populate)
		{
			$publisher->load();
		}
		return $publisher;
	}
//
//	/**
//	 * @param string $encrypted
//	 * @param bool $populate
//	 *
//	 * @throws \ErrorException
//	 * @return Publisher
//	 */
//	public static function loadByEncryption($encrypted, $populate = true)
//	{
//		//gWNtvpeQsAMUfqJd06mhvFdO
//		$path = base64x_decode($encrypted,static::ENCRYPTION_LOOKUP_TABLE);
//		return static::loadByPath($path,$populate);
//	}

	/**
	 * @return bool
	 */
	public function load()
	{
		if(!$this->isLoaded())
		{
			$buffer = Buffer::getConnection();
			$json   = $buffer->hGet(self::REDIS_KEY, $this->_id);
			if(is_string($json))
			{
				$data  = json_decode($json, true);
				$this->setBrand(new Brand($data[self::META_DATA_KEY_BRAND_ID]));
				$this->setUA($data[self::META_DATA_KEY_UA]);
				$this->setName($data[self::META_DATA_KEY_NAME]);
				$this->setPath($data[self::META_DATA_KEY_PATH]);
				$this->setAlias($data[self::META_DATA_KEY_ALIAS]);
				$this->setLandingPageIds($data[self::META_DATA_KEY_LANDING_PAGE_IDS]);
				if(isset($data[self::META_DATA_KEY_TYPE])) { $this->setType($data[self::META_DATA_KEY_TYPE]); }
				$this->_is_loaded = true;
			}
		}

		return $this->_is_loaded;
	}

	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * @param int $type
	 */
	public function setType($type)
	{
		$this->_type = $type;
	}

	/**
	 * @return boolean
	 */
	public function isLoaded()
	{
		return $this->_is_loaded;
	}

	/**
	 * @param string $ua
	 */
	public function setUA($ua)
	{
		$this->_ua = $ua;
	}

	/**
	 * @return string
	 */
	public function getUA()
	{
		return $this->_ua;
	}

	/**
	 * @return Brand
	 */
	public function getBrand()
	{
		return $this->_brand;
	}

	/**
	 * @param Brand $brand
	 */
	public function setBrand(Brand $brand)
	{
		$this->_brand = $brand;
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

	/**
	 * @param string $path
	 */
	public function setPath($path)
	{
		$this->_path = $path;
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->_path;
	}

	/**
	 * @param string $alias
	 */
	public function setAlias($alias)
	{
		$this->_alias = $alias;
	}

	/**
	 * @return string
	 */
	public function getAlias()
	{
		return $this->_alias;
	}

	/**
	 * @param string[] $landing_page_ids
	 */
	public function setLandingPageIds($landing_page_ids)
	{
		$this->_landing_page_ids = $landing_page_ids;
	}

	/**
	 * @return string[]
	 */
	public function getLandingPageIds()
	{
		return $this->_landing_page_ids;
	}

	/**
	 * @param array $ignore
	 * @return string
	 */
	public function getRandomRelatedLandingPageId(array $ignore)
	{
		$landingPageIds = array_values(array_diff($this->_landing_page_ids,$ignore));
		$winner = $this->_getRandomWinner($landingPageIds);
		if($winner === false)
		{
			$winner = $this->_getRandomWinner($this->_landing_page_ids);
		}
		return $winner;
	}

	/**
	 * @param array $candidates
	 * @return bool|mixed false on failure
	 */
	protected function _getRandomWinner(array $candidates)
	{
		$i = count($candidates);
		$randPosition = ($i > 1) ? mt_rand(0,$i-1) : 0 ;
		return isset($candidates[$randPosition]) ? $candidates[$randPosition] : false;
	}
}