<?php

namespace LPO;

use Request;

trait Attributable {

	/**
	 * @var Attributes
	 */
	protected $_attributes;

	/**
	 * @param Attributes $attributes
	 */
	public function __construct(Attributes $attributes)
	{
		$this->_attributes = $attributes;
	}

	/**
	 * @param string $countryCode
	 * @param string $language
	 * @param int $osId
	 * @param int $browserId
	 * @param int $publisherId
	 * @return string
	 */
	public static function getRedisKey($countryCode, $language, $osId, $browserId, $publisherId)
	{
		return implode(static::REDIS_KEY_DELIMITER,array($countryCode, $language, $osId, $browserId, $publisherId));
	}


	/**
	 * @return array
	 */
	protected function _getPermutationKeys()
	{
		$countryCode = $this->_attributes->getCountryCode();
		$language    = $this->_attributes->getLanguage();
		$osId        = $this->_attributes->getOsId();
		$browserId   = $this->_attributes->getBrowserId();
		$publisherId = $this->_attributes->getPublisherId();

		$defaultCountryCode = Request::COUNTRY_CODE_DEFAULT;
		$defaultLanguage    = Request::LANGUAGE_DEFAULT;
		$defaultOsId        = Request::OS_DEFAULT;
		$defaultBrowserId   = Request::BROWSER_DEFAULT;
		$defaultPublisherId = Publisher::DEFAULT_ID;

		$keys = array();
		for($i = 0; $i < 0b100000; $i++)
		{
			$a = (($i | 0b1    ) === $i) ? $countryCode : $defaultCountryCode;
			$b = (($i | 0b10   ) === $i) ? $language    : $defaultLanguage;
			$c = (($i | 0b100  ) === $i) ? $osId        : $defaultOsId;
			$d = (($i | 0b1000 ) === $i) ? $browserId   : $defaultBrowserId;
			$e = (($i | 0b10000) === $i) ? $publisherId : $defaultPublisherId;

			$keys[static::getRedisKey($a, $b, $c, $d, $e)] = true;
		}

		return $keys;
	}


	/**
	 * @param mixed[] $pool
	 * @param string $delimiter
	 *
	 * @return mixed
	 */
	protected function _chooseFromAttributeConflict(array $pool,$delimiter)
	{
		$defaultPublisherId = Publisher::DEFAULT_ID;
		$defaultOsId 		= Request::OS_DEFAULT;
		$defaultBrowserId 	= Request::BROWSER_DEFAULT;
		$defaultLanguage 	= Request::LANGUAGE_DEFAULT;
		$defaultCountryCode = Request::COUNTRY_CODE_DEFAULT;

		$maxWeight = -1;
		$rule = current($pool);

		foreach ($pool as $key=>$obj)
		{
			$weight = 0;
			list($countryCode, $language, $osId, $browserId, $publisherId) = explode($delimiter,$key);

			if($osId        != $defaultOsId)       {$weight |= 0b1;} // os_id     => 1
			if($language    != $defaultLanguage)   {$weight |= 0b10;} // language  => 2
			if($browserId   != $defaultBrowserId)  {$weight |= 0b100;} // browser   => 4
			if($countryCode != $defaultCountryCode){$weight |= 0b1000;} // country   => 8
			if($publisherId != $defaultPublisherId){$weight |= 0b10000;} // publisher => 16

			if($weight > $maxWeight) { $rule = $obj; }
		}

		return $rule;
	}
} 