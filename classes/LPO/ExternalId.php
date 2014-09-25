<?php
namespace LPO;

use QueryString;

class ExternalId {

	/**
	 *
	 */
	const GET_PARAM_ID = 'id';
	/**
	 *
	 */
	const REGEX_GET_PARAM_ID = '/^[0-9]{1,19}$/';
	const REDIS_KEY_INCREMENTAL_ID = 'lpo_external_id';
	/**
	 * @var string
	 */
	protected $_id;

	/**
	 * @param null|int|string $id
	 */
	function __construct($id = null)
	{
		$intId = (int)$id;
		if(is_null($id) || !is_numeric($id) || $intId > PHP_INT_MAX)
		{
			$id = $this->_getUniqueBigInt();
		}
		$this->_id = (string)$id;
	}

	/**
	 * @return null|string
	 */
	public static function getFromRequest()
	{
		$var = QueryString::getInstance()->getParam(static::GET_PARAM_ID);
		return (isset($var) && preg_match(self::REGEX_GET_PARAM_ID, $var)) ? $var : null;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @return string
	 */
	protected function _getUniqueBigInt()
	{
		return _CLOCK64();
	}
}