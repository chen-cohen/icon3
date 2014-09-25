<?php

namespace LPO;

use QueryString;
use Request;

class Attributes {

	const GET_PARAM_COUNTRY_CODE = 'country_code';
	const GET_PARAM_LANGUAGE     = 'language';
	const GET_PARAM_OS_ID        = 'os_id';
	const GET_PARAM_BROWSER_ID   = 'browser_id';

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
	 * @param $publisherId
	 * @param int $browserId
	 * @param string $countryCode
	 * @param string $language
	 * @param int $osId
	 */
	function __construct($publisherId,$browserId = null, $countryCode = null,$language = null,$osId = null)
	{
		$this->_publisher_id = $publisherId;
		if(IS_SANDBOX)
		{
			$queryString = QueryString::getInstance();
			$this->_country_code = $queryString->getParam(static::GET_PARAM_COUNTRY_CODE,Request::getCountry());
			$this->_language     = $queryString->getParam(static::GET_PARAM_LANGUAGE,Request::getLanguage());
			$this->_os_id        = $queryString->getParam(static::GET_PARAM_OS_ID,Request::getOS());
			$this->_browser_id   = $queryString->getParam(static::GET_PARAM_BROWSER_ID,Request::getBrowser());
		}
		else
		{
			$this->_country_code = $countryCode           ?            : Request::getCountry();
			$this->_language     = $language              ?            : Request::getLanguage();
			$this->_os_id        = is_numeric($osId)      ? $osId      : Request::getOS();
			$this->_browser_id   = is_numeric($browserId) ? $browserId : Request::getBrowser();
		}
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
	 * @param string $country_code
	 */
	public function setCountryCode($country_code)
	{
		$this->_country_code = $country_code;
	}

	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		$this->_language = $language;
	}

	/**
	 * @param int $os_id
	 */
	public function setOsId($os_id)
	{
		$this->_os_id = $os_id;
	}

	/**
	 * @param int $publisherId
	 */
	public function setPublisherId($publisherId)
	{
		$this->_publisher_id = $publisherId;
	}

	/**
	 * @return string
	 */
	public function getCountryCode()
	{
		return $this->_country_code;
	}

	/**
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->_language;
	}

	/**
	 * @return int
	 */
	public function getOsId()
	{
		return $this->_os_id;
	}

	/**
	 * @return int
	 */
	public function getPublisherId()
	{
		return $this->_publisher_id;
	}

}