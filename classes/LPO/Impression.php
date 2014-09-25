<?php

namespace LPO;

use LPO\Db\Redis\Buffer as Buffer;
use InvalidArgumentException;
use RedisException;
use Request;
use UnexpectedValueException;

class Impression {

	/**
	 * action_id bit-mask
	 */
	const TYPE_BANNER 				= 0b00001; // 1
	const TYPE_LANDING_PAGE 		= 0b00010; // 2
	const TYPE_DOWNLOADED 			= 0b00100; // 4
	const TYPE_INSTALLER_DOWNLOADED = 0b01000; // 8
	const TYPE_INSTALLER_STARTED 	= 0b10000; // 16
	/**
	 *
	 */
	const MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';
	/**
	 * Redis key for impressions list
	 */
	const REDIS_KEY_IMPRESSION = 'lpo_impression';
	/**
	 * @var int - unsigned
	 */
	protected $_type;
	/**
	 * @var int - unsigned
	 */
	protected $_publisher_id;
	/**
	 * @var ExternalId
	 */
	protected $_external_id;
	/**
	 * @var Banner
	 */
	protected $_banner;
	/**
	 * @var LandingPage
	 */
	protected $_landing_page;

	/**
	 * @param int $type
	 * @param ExternalId $externalId
	 * @param int $publisherId
	 * @param \LPO\Banner $banner $banner
	 * @param \LPO\LandingPage $landingPage $landingPage
	 * @internal param $int $publisher_id
	 */
	public function __construct($type, ExternalId $externalId, $publisherId = Publisher::DEFAULT_ID, Banner $banner = null, LandingPage $landingPage = null)
	{
		$this->_validateArguments($type, $externalId, $publisherId, $banner, $landingPage);

		$this->_type = $type;
		$this->_external_id = $externalId;
		$this->_publisher_id = $publisherId;

		$this->_banner = is_null($banner) ? new Object(Banner::DEFAULT_ID) : $banner;
		$this->_landing_page = is_null($landingPage) ? new Object(LandingPage::DEFAULT_ID) : $landingPage;
	}

	/**
	 * @return int
	 * @throws RedisException
	 */
	public function save()
	{
		$buffer = Buffer::getConnection();
		$successful = $buffer->rPush(static::REDIS_KEY_IMPRESSION, $this->_getImpressionString());
		if(!$successful)
		{
			$lastError = $buffer->getLastError();
			$buffer->clearLastError();
			throw new RedisException($lastError);
		}
		return $successful;
	}

	/**
	 * @return string
	 */
	protected function _getImpressionString()
	{
		// columns: external_id,report_time,country_code,lang,ip,os_id,browser_id,banner_id,lp_id,publisher_id,action_id
		return sprintf("%d,'%s','%s','%s',%s,%d,%d,%d,%d,%d,%d",

			$this->_external_id->getId(),
			date(static::MYSQL_DATETIME_FORMAT),

			// attributes
			Request::getCountry(),
			Request::getLanguage(),
			ip2long(Request::getIp()),
			Request::getOS(),
			Request::getBrowser(),

			$this->_banner->getId(),
			$this->_landing_page->getId(),
			$this->_publisher_id,
			$this->_type
		);
	}

	/**
	 * @param int $type
	 * @return bool
	 * @throws UnexpectedValueException
	 */
	protected function _validateType($type)
	{
		// acts as a white-list
		switch($type)
		{
			case ($type === static::TYPE_BANNER):
			case ($type === static::TYPE_LANDING_PAGE):
			case ($type === static::TYPE_DOWNLOADED):
				return true;
				break;
			default:
				throw new UnexpectedValueException('provided type is not valid.');
				break;
		}
	}

	/**
	 * @param ExternalId $externalId
	 * @return bool
	 * @throws UnexpectedValueException
	 */
	protected function _validateExternalId(ExternalId $externalId)
	{
		if(is_null($externalId))
		{
			throw new UnexpectedValueException('externalId must be an instance of ExternalId');
		}

		$id = $externalId->getId();
		if(!is_numeric($id))
		{
			throw new UnexpectedValueException('externalId obj should contain an integer. provided with: ' . $id);
		}
	}

	/**
	 * @param $publisherId
	 * @throws UnexpectedValueException
	 */
	protected function _validatePublisherId($publisherId)
	{
		if(is_null($publisherId) || !is_int($publisherId) || $publisherId < 0)
		{
			throw new UnexpectedValueException('publisher id should be an unsigned integer. provided with: ' . $publisherId);
		}
	}

	/**
	 * @param int $type
	 * @param Banner $banner
	 * @param LandingPage $landingPage
	 * @throws InvalidArgumentException
	 */
	protected function _validateTypeAndObjectsConstraints($type, Banner $banner = null, LandingPage $landingPage = null)
	{
		// type banner without Banner and LandingPage objects
		if($type === static::TYPE_BANNER && (is_null($banner) || is_null($landingPage) ))
		{
			throw new InvalidArgumentException('banner typed impression must be provided with both Banner and LandingPage objects!');
		}

		// type landing page without LandingPage object
		if($type === static::TYPE_LANDING_PAGE && is_null($landingPage))
		{
			throw new InvalidArgumentException('landing page typed impression must be provided with a LandingPage object!');
		}
	}

	/**
	 * @param $type
	 * @param ExternalId $externalId
	 * @param $publisherId
	 * @param Banner $banner
	 * @param LandingPage $landingPage
	 */
	protected function _validateArguments($type, ExternalId $externalId, $publisherId = 0, Banner $banner = null, LandingPage $landingPage = null)
	{
		$this->_validateType($type);
		$this->_validateExternalId($externalId);
		$this->_validatePublisherId($publisherId);
		$this->_validateTypeAndObjectsConstraints($type, $banner, $landingPage);
	}
}
