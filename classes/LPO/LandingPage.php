<?php
namespace LPO;

use QueryString;

class LandingPage extends Object {

	const FALLBACK_ID  = 1380455955;
	const DEFAULT_ID   = 0;
	const GET_PARAM_ID = 'lpid';

	/**
	 * @return int|null
	 */
	public static function getFromRequest()
	{
		return QueryString::getInstance()->getParam(static::GET_PARAM_ID);
	}

//	/**
//	 * @return string
//	 * @todo: implement for banner support
//	 */
//	protected function _getUrl()
//	{
//		return 'http://'.DOMAIN.'/lp.php';
//	}

//	/**
//	 * @param ExternalId $externalId
//	 * @return string
//	 * @todo: implement for banner support
//	 */
//	public function getUrl(ExternalId $externalId)
//	{
//		$queryString = http_build_query(array(
//			self::GET_PARAM_ID       => $this->getId(),
//			ExternalId::GET_PARAM_ID => $externalId->getId(),
//		));
//
//		return $this->_getUrl(). '?'. $queryString;
//	}
}
