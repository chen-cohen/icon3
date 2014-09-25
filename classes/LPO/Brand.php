<?php

namespace LPO;

use Exception;
use LPO\Db\Redis\Buffer;

class Brand {

	const REDIS_KEY     = 'lpo_brand';
	const TEST_BRAND_ID = '__TEST__';

	const KEY_NAME  = 'name';
	const KEY_TEXT  = 'text';
	const KEY_LINKS = 'links';

	const ID_VAUDIX = 'Vaudix';

	/**
	 * @var string
	 */
	protected $_id;
	/**
	 * @var string
	 */
	protected $_links;
	/**
	 * @var string
	 */
	protected $_name;
	/**
	 * @var string
	 */
	protected $_text;
	/**
	 * @var bool
	 */
	protected $_is_loaded = false;

	/**
	 * @param string $links
	 */
	public function setLinks($links)
	{
		$this->_links = $links;
	}

	/**
	 * @return string
	 */
	public function getLinks()
	{
		return $this->_links;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->_name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param string $text
	 */
	public function setText($text)
	{
		$this->_text = $text;
	}

	/**
	 * @return string
	 */
	public function getText()
	{
		return $this->_text;
	}
	/**
	 * @param string $id
	 */
	public function __construct($id)
	{
		$this->_id = $id;
	}

	public function load()
	{
		$brandJson = Buffer::getConnection()->hGet(self::REDIS_KEY, $this->_id);
		$brand     = json_decode($brandJson, true);

		$this->_name  = $brand[static::KEY_NAME];
		$this->_text  = $brand[static::KEY_TEXT];
		$this->_links = $brand[static::KEY_LINKS];

		$this->_is_loaded = true;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param string $id
	 * @param string $name
	 * @param string $text
	 * @param string $links
	 *
	 * @return Brand
	 */
	public static function factory($id, $name, $text, $links)
	{
		$instance = new self($id);
		$instance->setName($name);
		$instance->setText($text);
		$instance->setLinks($links);
		$instance->_is_loaded = true;
		return $instance;
	}

	/**
	 * @return bool
	 */
	protected function isLoaded()
	{
		return $this->_is_loaded;
	}
}