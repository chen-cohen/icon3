<?php

namespace LPO;

use Request;
use LPO\Db\Redis\Buffer;
use LPO\Persistence\DownloadMethod as PersistenceDownloadMethod;

class DownloadMethodRule {

	const VALUE_DELIMITER        = ';';
	const REDIS_KEY_DELIMITER = '_';
	const REDIS_KEY = 'lpo_download_method_rule';

	/**
	 * @param int $browserId
	 * @param string $countryCode
	 * @param int $publisherId
	 *
	 * @return string
	 */
	public static function getRedisKey($browserId, $countryCode, $publisherId)
	{
		return implode(static::REDIS_KEY_DELIMITER,array($browserId, $countryCode, $publisherId));
	}

	/**
	 * @param Attributes $attributes
	 *
	 * @return array
	 */
	public static function getPermutations(Attributes $attributes)
	{
		$countryCode = $attributes->getCountryCode();
		$browserId   = $attributes->getBrowserId();
		$publisherId = $attributes->getPublisherId();

		$defaultCountryCode = Request::COUNTRY_CODE_DEFAULT;
		$defaultBrowserId   = Request::BROWSER_DEFAULT;
		$defaultPublisherId = Publisher::DEFAULT_ID;

		$keys = array();
		for($i = 0; $i < 8; $i++)
		{
			$a = (($i | 1) === $i) ? $browserId : $defaultBrowserId;
			$b = (($i | 2) === $i) ? $countryCode : $defaultCountryCode;
			$c = (($i | 4) === $i) ? $publisherId : $defaultPublisherId;

			$keys[] = static::getRedisKey($a, $b, $c);
		}

		return Buffer::getConnection()->hMGet(static::REDIS_KEY, $keys);
	}

	/**
	 * @param array $results
	 *
	 * @return int
	 */
	public static function getDownloadMethodIdFromRules(array $results)
	{
		$maxPriority = -1;
		$maxPriorityRuleMask = -1;
		$finalDownloadMethodId = PersistenceDownloadMethod::__default;

		foreach ($results as $ruleKey => $result)
		{
			// if result is null or false, skip
			if (!$result){ continue; }

			list($priority,$downloadMethodId) = explode(static::VALUE_DELIMITER,$result);

			$priority         = (int)$priority;
			$downloadMethodId = (int)$downloadMethodId;

			// lower priority
			if($priority < $maxPriority){ continue; }

			$priorityRuleMask = 0;
			list($browserId,$countryCode,$publisherId) = explode(static::REDIS_KEY_DELIMITER,$ruleKey);

			$browserId   = (int)$browserId;
			$publisherId = (int)$publisherId;

			if($browserId   !== Request::BROWSER_DEFAULT)      { $priorityRuleMask |= 0b100; }
			if($countryCode !== Request::COUNTRY_CODE_DEFAULT) { $priorityRuleMask |= 0b010; }
			if($publisherId !== Publisher::DEFAULT_ID)         { $priorityRuleMask |= 0b001; }

			if(	// higher priority
				$priority > $maxPriority ||
				// or equal priority and higher bit-mask
				($priority === $maxPriority && $maxPriorityRuleMask < $priorityRuleMask)
			)
			{
				$maxPriority = $priority;
				$finalDownloadMethodId = $downloadMethodId;
				$maxPriorityRuleMask = $priorityRuleMask;
				continue;
			}
		}

		return $finalDownloadMethodId;
	}
}