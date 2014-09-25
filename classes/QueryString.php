<?php

class QueryString {

	/**
	 * @var QueryString
	 */
	private static $_instance;
	/**
	 * @var mixed[]
	 */
	private $_data;

	private static $overrideParams = array(
		'tid'=>'affiliate_id'
	);

	private function __construct()
	{
		$this->_data = $_GET;
		$this->_overrideParams();
	}

	/**
	 * @return QueryString
	 */
	public static function getInstance()
	{
		if(is_null(static::$_instance))
		{
			static::$_instance = new QueryString();
		}

		return static::$_instance;
	}

	/**
	 * @return mixed[]
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function setParam($key, $value)
	{
		$this->_data[$key] = $value;
	}

	/**
	 * @param string $key
	 * @param mixed|null $defaultValue
	 * @return mixed|null
	 */
	public function getParam($key,$defaultValue = null)
	{
		return isset($this->_data[$key]) && !is_null($this->_data[$key]) ? $this->_data[$key] : $defaultValue;
	}

	private function _overrideParams()
	{
		foreach(static::$overrideParams as $source => $target)
		{
			if(isset($this->_data[$source]))
			{
				$this->_data[$target] = $this->_data[$source];
			}
		}
	}
}
 