<?php

namespace LPO\Persistence;

use Db\Adapter\Pdo\Dbase;

class Publisher extends CRUD {

	/**
	 * @var string
	 */
	const TABLE_NAME = 'lpo_db_ORM.publisher';
	/**
	 * @var int
	 */
	protected $_id;
	/**
	 * @var int
	 */
	protected $_type_id;
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
	 * @var int
	 */
	protected $_brand_id;
	/**
	 * @var Configuration
	 */
	protected $_download_domain;
	/**
	 * @var Rule[]
	 */
	protected $_rules;
	/**
	 * @var LandingPage[]
	 */
	protected $_landing_pages;
	/**
	 * @var int[]
	 */
	protected $_landing_page_ids;

	/**
	 * @param array $data
	 */


	protected function __construct(array $data)
	{
		$this->_id       = (int)$data['id'];
		$this->_ua       = trim((string)$data['ua']);
		$this->_name     = trim((string)$data['name']);
		$this->_path     = trim((string)$data['path']);
		$this->_brand_id = (int)$data['brand_id'];

		if(isset($data['type_id']) && !empty($data['type_id']))
		{
			$this->_type_id = (int)$data['type_id'];
		}

		if(isset($data['alias']) && !empty($data['alias']))
		{
			$this->_alias = trim((string)$data['alias']);
		}

		if(isset($data['landing_page_id']) && is_array($data['landing_page_id']))
		{
			$this->_landing_page_ids = $data['landing_page_id'];
		}
	}

	/**
	 * @param array $data
	 *
	 * @param bool $lazy
	 *
	 * @return Publisher
	 */
	public static function factory($data,$lazy = false)
	{
		/**
		 * @var Publisher $instance
		 */
		$instance = parent::factory($data);
		if($lazy) {return $instance;}
		$instance->_loadBrand();
		$instance->_loadDownloadDomain();
		return $instance;
	}

	/**
	 * @return mixed
	 */
	public function create()
	{
		$adapter = Dbase::getConnection();
		$stmt    = $adapter->prepare('INSERT INTO '.static::TABLE_NAME.'(`id`,`type_id`,`name`,`path`,`alias`,`ua`,`brand_id`) VALUES (:id,:type_id,:name,:path,:alias,:ua,:brand_id)');

		$stmt->bindValue(':id', $this->_id);
		$stmt->bindValue(':type_id', $this->_type_id);
		$stmt->bindValue(':name', $this->_name);
		$stmt->bindValue(':path', $this->_path);
		$stmt->bindValue(':alias', $this->_alias);
		$stmt->bindValue(':ua', $this->_ua);
		$stmt->bindValue(':brand_id', $this->_brand_id);

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
		$stmt    = $adapter->prepare('UPDATE '.static::TABLE_NAME.' SET `type_id`=:type_id ,`name`=:name ,`path`=:path ,`alias`=:alias ,`ua`=:ua,`brand_id`=:brand_id WHERE `id` = :id');

		$stmt->bindValue(':id', $this->_id);
		$stmt->bindValue(':type_id', $this->_type_id);
		$stmt->bindValue(':ua', $this->_ua);
		$stmt->bindValue(':name', $this->_name);
		$stmt->bindValue(':path', $this->_path);
		$stmt->bindValue(':alias', $this->_alias);
		$stmt->bindValue(':brand_id', $this->_brand_id);

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
	 * @param string $path
	 *
	 * @return static
	 */
	public function loadByPath($path)
	{
		return static::loadBy('path', $path);
	}

	/**
	 * @return int
	 */
	public function getTypeId()
	{
		return $this->_type_id;
	}

	/**
	 * @param int $type
	 */
	public function setTypeId($type)
	{
		$this->_type_id = $type;
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
	 * @return string
	 */
	public function getDownloadDomain()
	{
		if(is_null($this->_download_domain))
		{
			$this->_loadDownloadDomain();
		}
		return $this->_download_domain;
	}

	/**
	 * @param string $downloadDomainUrl
	 * @return mixed
	 */
	public function updateDownloadDomain($downloadDomainUrl)
	{
		$downloadDomain = Configuration::factory(
			array(
				 'key'          => Configuration::KEY_DOWNLOAD_DOMAIN,
				 'publisher_id' => $this->_id,
				 'value'        => $downloadDomainUrl,
				 'description'  => 'Download Domain')
		);

		return $downloadDomain->update();
	}

	/**
	 * @return int
	 */
	public function getBrandId()
	{
		return $this->_brand_id;
	}

	/**
	 * @param int $brandId
	 */
	public function setBrandId($brandId)
	{
		$this->_brand_id = $brandId;
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
	 *
	 */
	protected function _loadBrand()
	{
		$this->_brand = Brand::loadBy('id', $this->_brand_id);
	}
	/**
	 *
	 */
	protected function _loadDownloadDomain()
	{
		$this->_download_domain = Configuration::loadByKeyAndPublisher(Configuration::KEY_DOWNLOAD_DOMAIN, $this->_id);
	}

	/**
	 * @return Rule[]
	 */
	public function getRelatedRules()
	{
		if(is_null($this->_rules))
		{
			$adapter      = Dbase::getConnection();
			$PDOStatement = $adapter->prepare('SELECT tbl.* FROM '.Rule::TABLE_NAME.' AS `tbl` WHERE tbl.publisher_id = :value and `user_defined` = "1"');

			$PDOStatement->bindValue(':value', $this->_id);

			$PDOStatement->execute();
			$data = $PDOStatement->fetchAll($adapter::FETCH_ASSOC);

			if(!$data)
			{
				return null;
			}

			$collection = array();
			foreach($data as $element)
			{
				$collection[] = Rule::factory($element,true);
			}

			$this->_rules = $collection;
		}

		return $this->_rules;
	}


	/**
	 * @return array
	 */
	public function getRelatedLandingPageIds()
	{
		//todo: refactor
		if(is_null($this->_landing_page_ids))
		{
			$adapter = Dbase::getConnection();
			$stmt    = $adapter->prepare('SELECT landing_page_id FROM '.PublisherLandingPage::TABLE_NAME.' WHERE publisher_id = :publisher_id');

			$stmt->bindValue(':publisher_id', $this->_id);
			$stmt->execute();

			$landingPageIds = $stmt->fetchAll($adapter::FETCH_COLUMN);
			$this->_landing_page_ids = $landingPageIds;
		}

		return $this->_landing_page_ids;
	}

	/**
	 * @return LandingPage[]
	 */
	public function getRelatedLandingPages()
	{
		if(is_null($this->_landing_pages))
		{
			$landingPageIds = $this->getRelatedLandingPageIds();
			$landingPages = array();
			foreach($landingPageIds as $lpId)
			{
				$landingPages[$lpId] = LandingPage::loadBy('id',$lpId);
			}

			$this->_landing_pages = $landingPages;
		}

		return $this->_landing_pages;
	}


	public function updateLandingPages()
	{
		$adapter = Dbase::getConnection();
		$relatedLandingPageIds = $this->getRelatedLandingPageIds();
		$pairs = array();
		foreach($relatedLandingPageIds as $relatedLandingPageId)
		{
			$pairs[] = $relatedLandingPageId.','.$this->_id;
		}

		$stmt = $adapter->prepare('INSERT INTO '.PublisherLandingPage::TABLE_NAME.' (`landing_page_id`,`publisher_id`) VALUES ('.implode('),(',$pairs).')');

		return $stmt->execute();
	}

	public function getConfigurations(){
		return Configuration::loadAllKeysValuesByPublisher($this->getId());
	}

	public function updatePublisherConfiguration()
	{
		$adapter = Dbase::getConnection();
		$stmt = $adapter->prepare('INSERT INTO '.PublisherLandingPage::TABLE_NAME.' (`landing_page_id`,`publisher_id`) VALUES ('.implode('),(',$pairs).')');

		return $stmt->execute();
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

}