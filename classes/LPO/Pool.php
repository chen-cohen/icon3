<?php

namespace LPO;

use Util;
use ErrorException;
use LPO\Db\Redis\Buffer;

class Pool {

	use Attributable;

	const MAX_SUM = 10000;

	const REDIS_KEY              = 'lpo_rule';
	const REDIS_KEY_DELIMITER    = '_';
	const REDIS_KEY_USER_DEFINED = 'lpo_user_defined_rule';

	const KEY_LANDING_PAGE = 'lp';
	const KEY_BANNER       = 'banner';
	const KEY_PRIORITY     = 'priority';

	/**
	 * @var array
	 */
	protected $_pool;
	/**
	 * @var array
	 */
	protected $_results;
	/**
	 * @var array
	 */
	protected $_flat_pool;
	/**
	 * @var array
	 */
	protected $_banner_pool;
	/**
	 * @var array
	 */
	protected $_lp_pool;
	/**
	 * @var int
	 */
	protected $_lp_sum;
	/**
	 * @var int
	 */
	protected $_banner_sum;

	/**
	 *
	 */
	protected function _applyWinnersLogic()
	{
		$rule = $this->getChosenRule();
		$this->_banner_pool = $rule[static::KEY_BANNER];
		$this->_lp_pool     = $rule[static::KEY_LANDING_PAGE];
		if(!$this->_lp_pool)
		{
			//fixme: ??!?!
			$this->_lp_pool = array();
		}
		$this->_banner_sum  = static::MAX_SUM;//array_sum($rule[static::KEY_BANNER]);
		$this->_lp_sum      = static::MAX_SUM;//array_sum($rule[static::KEY_LANDING_PAGE]);
	}

	/**
	 * @return mixed
	 */
	public function getBannerWinner()
	{
		if(is_null($this->_banner_pool) || is_null($this->_banner_sum))
		{
			$this->_applyWinnersLogic();
		}
		return $this->_getWinner($this->_banner_pool,$this->_banner_sum);
	}

	/**
	 * @return mixed
	 */
	public function getLandingPageWinner()
	{
		if(is_null($this->_lp_pool) || is_null($this->_lp_sum))
		{
			$this->_applyWinnersLogic();
		}

		## DO NOT PUT ANY VALUE BIGGER THEN 4.5 BECAUSE OF BIG INT OVERFLOW!
		$expo = 2;
		$sum = 0;
		foreach ($this->_lp_pool as &$a) {
			$a = pow($a,$expo);
			$sum+=$a;
		}

		return $this->_getWinner($this->_lp_pool,$sum);
	}

	/**
	 * @return array
	 */
	protected function _getPermutations()
	{
		$keys = $this->_getPermutationKeys();
		$redis = Buffer::getConnection();

		$useUserDefined = false;
		$userDefinedResults = $redis->hMGet(static::REDIS_KEY_USER_DEFINED,array_keys($keys));
		foreach($userDefinedResults as $value)
		{
			if(!$value) continue;
			$useUserDefined = true;
		}

		$results = ($useUserDefined) ? $userDefinedResults : $redis->hMGet(static::REDIS_KEY,array_keys($keys));
		return $results;
	}

	/**
	 * @param array $results
	 * @return array
	 * @throws ErrorException
	 */
	protected function _getPool(array $results)
	{
		$maxPriority = 0;
		$pool = array();
		foreach ($results as $key=>$result)
		{
			// if result is null or false, skip
			if (!$result){ continue; }

			$obj = json_decode($result, true);

			// json parsing error
			if (is_null($obj))
			{
				throw new ErrorException(Util::translateJsonErrorCode(json_last_error()));
			}

			// if no priority, skip
			if (!isset($obj[static::KEY_PRIORITY]))
			{
				continue;
			}

			// if greater than maxPriority
			if ($obj[static::KEY_PRIORITY] > $maxPriority)
			{
				$maxPriority = $obj[static::KEY_PRIORITY];
				$pool = array($key=>$obj);
			}
			// if equal to maxPriority
			else if ($obj[static::KEY_PRIORITY] == $maxPriority)
			{
				$pool[$key] = $obj;
			}
			// if less than maxPriority
			else
			{
				continue;
			}
		}
		return $pool;
	}

	/**
	 * @param array $candidates
	 * @param int $sum
	 * @param null|int $rand
	 * @return bool|int|mixed|string
	 */
	protected function _getWinner(array $candidates, $sum, $rand = null)
	{
		if(is_null($rand)){ $rand = mt_rand(0,$sum); }
		$counter = 0;
		foreach ($candidates as $id=>$candidate)
		{
			if($counter <= $rand && $rand <= ($counter+$candidate))
			{
				return $id;
			}
			$counter += $candidate;
		}

//		$candidate = 0;
//		if($counter <= $rand && $rand <= ($counter+$candidate))
//		{
//			return key(end($candidates));
//		}

		return false;// assume leftovers
	}

	/**
	 * @return mixed
	 */
	public function getChosenRule()
	{
		$this->_results = $this->_getPermutations();
		$this->_pool = $this->_getPool($this->_results);
		$rule = (count($this->_pool) > 1 ?
			$this->_chooseFromAttributeConflict($this->_pool,Pool::REDIS_KEY_DELIMITER) :
			current($this->_pool)
		);
		return $rule;
	}

	/**
	 * @return array
	 */
	public function getPoolLandingPageIds()
	{
		if(empty($this->_lp_pool))
		{
			return false;
		}

		return array_keys($this->_lp_pool);
	}
}