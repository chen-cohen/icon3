<?php

namespace LPO;


use QueryString;
use Util;

class DownloadUrl {

	const GET_PARAM_EXTERNAL_ID = 'external_id';
	const GET_PARAM_REDIRECT_URL = 'r';

	/**
	 * @var string
	 */
	protected $_redirect_url;
	/**
	 * @var ExternalId
	 */
	protected $_external_id;

	/**
	 * @param string $redirectUrl
	 * @param ExternalId $externalId
	 */
	public function __construct($redirectUrl,ExternalId $externalId)
	{
		$this->_redirect_url = $redirectUrl;
		$this->_external_id = $externalId;
	}


	/**
	 * @return null|string
	 */
	public static function getRedirectUrlFromRequest()
	{
		return QueryString::getInstance()->getParam(static::GET_PARAM_REDIRECT_URL);
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		$this->_redirect_url .= (strpos($this->_redirect_url,'?')!==false) ? '&' : '?';
		return $this->_redirect_url. static::GET_PARAM_EXTERNAL_ID . '=' .$this->_external_id->getId();
	}
}