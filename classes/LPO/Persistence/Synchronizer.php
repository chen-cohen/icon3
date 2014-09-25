<?php

namespace LPO\Persistence;


use Db\Adapter\Pdo\Dbase;
use LPO\DownloadMethodRule as RT_DownloadMethodRule;
use LPO\Pool;
use LPO\Bootstrap;
use LPO\Brand as RT_Brand;
use LPO\Publisher as RT_Publisher;
use LPO\PublisherType as RT_PublisherType;
use LPO\Injectable as RT_Injectable;
use LPO\Configuration as RT_Configuration;

use LPO\Db\Redis\ProtocolGenerator as Redis;
use SplDoublyLinkedList;
use SplQueue;

class Synchronizer {

	const REDIS_KEY_LOCK = 'lpo_lock';

	/**
	 * @var Redis[]
	 */
	protected $_connections;
	/**
	 * @var SplQueue
	 */
	protected $_queue;

	/**
	 * @param Redis[] $connections
	 */
	function __construct(array $connections)
	{
		$this->_connections = $connections;
		$this->_queue = new SplQueue();
		$this->_queue->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);
	}

	/**
	 * @return bool
	 */
	public function isOptimizationRunning()
	{
		$persistence = Dbase::getConnection();
		$value = $persistence->query('select `value` from `lpo_db_ORM`.`lpo_run_state` WHERE `key` = "is_running" LIMIT 1')->fetchColumn(0);
		return ($value == '1' ? true : false);
	}

	/**
	 *
	 */
	public function brands()
	{
		$this->_bulkAction(function (Redis $redisConnection)
		{
			$redisConnection->del(RT_Brand::REDIS_KEY);
		},array());

		$brands = Brand::loadAll();
		foreach($brands as $brand)
		{
			$json = json_encode(array(RT_Brand::KEY_NAME => $brand->getName(), RT_Brand::KEY_TEXT => $brand->getText(), RT_Brand::KEY_LINKS => $brand->getLinks()));
			$this->_bulkAction(
				function (Redis $redisConnection, array $args)
				{
					/**
					 * @var Brand $brand
					 */
					list($brand, $json) = $args;
					$redisConnection->hSet(RT_Brand::REDIS_KEY, $brand->getName(), $json);
				},
				array($brand, $json)
			);
		}
	}

	/**
	 *
	 */
	public function publishers()
	{
		$this->_bulkAction(function (Redis $redisConnection)
		{
			$redisConnection->del(RT_Publisher::REDIS_KEY);
			$redisConnection->del(RT_Publisher::REDIS_KEY_PATH_MAP);
		},array());

		$publishers = Publisher::loadAll(false);
		foreach($publishers as $publisher)
		{
			$landingPageIds = $publisher->getRelatedLandingPageIds();
			$json = json_encode(
				array(
					 RT_Publisher::META_DATA_KEY_UA               => $publisher->getUA(),
					 RT_Publisher::META_DATA_KEY_NAME             => $publisher->getName(),
					 RT_Publisher::META_DATA_KEY_PATH             => $publisher->getPath(),
					 RT_Publisher::META_DATA_KEY_ALIAS            => $publisher->getAlias(),
					 RT_Publisher::META_DATA_KEY_TYPE             => $publisher->getTypeId(),
					 RT_Publisher::META_DATA_KEY_BRAND_ID         => $publisher->getBrand()->getName(),
					 RT_Publisher::META_DATA_KEY_LANDING_PAGE_IDS => (!$landingPageIds ? [] : $landingPageIds),
				)
			);

			$this->_bulkAction(
				function (Redis $redisConnection, array $args)
				{
					/**
					 * @var Publisher $publisher
					 */
					list($publisher, $json) = $args;
					$redisConnection->hSet(RT_Publisher::REDIS_KEY, $publisher->getId(), $json);
					$redisConnection->hSet(RT_Publisher::REDIS_KEY_PATH_MAP, mb_strtolower($publisher->getPath()), $publisher->getId());
					$redisConnection->hSet(RT_Publisher::REDIS_KEY_PATH_MAP, mb_strtolower($publisher->getAlias()), $publisher->getId());
				},
				array($publisher, $json)
			);
		}
	}
	/**
	 *
	 */
	public function publisherTypes()
	{
		$this->_bulkAction(function (Redis $redisConnection)
		{
			$redisConnection->del(RT_PublisherType::REDIS_KEY);
		},array());

		$publisherTypes = PublisherType::loadAll(false);
		foreach($publisherTypes as $publisherType)
		{
			$this->_bulkAction(
				function (Redis $redisConnection, array $args)
				{
					/**
					 * @var PublisherType $publisherType
					 */
					list($publisherType) = $args;
					$redisConnection->hSet(RT_PublisherType::REDIS_KEY, $publisherType->getId(), $publisherType->getName());
				},
				array($publisherType)
			);
		}
	}

	/**
	 *
	 */
	public function expiredDomains()
	{
		$this->_bulkAction(function (Redis $redisConnection) { $redisConnection->del(Bootstrap::REDIS_KEY_EXPIRED_DOMAIN); },array());

		$expiredDomains = ExpiredDomain::loadAll(true);
		foreach($expiredDomains as $domain)
		{
			$this->_bulkAction(
				function (Redis $redisConnection, array $args)
				{
					/**
					 * @var ExpiredDomain $domain
					 */
					list($domain) = $args;
					$redisConnection->hSet(Bootstrap::REDIS_KEY_EXPIRED_DOMAIN, $domain->getOrigin(), $domain->getTarget());
				},
				array($domain)
			);
		}
	}

	/**
	 *
	 */
	public function configurations()
	{
		// clear previous
		$this->_bulkAction(function (Redis $redisConnection) { $redisConnection->del(RT_Configuration::REDIS_KEY); },array());

		$configurations = Configuration::loadAll(true);
		foreach($configurations as $config)
		{
			$this->_bulkAction(
				function (Redis $redisConnection, array $args)
				{
					/**
					 * @var Configuration $config
					 */
					list($config) = $args;
					$redisConnection->hSet(RT_Configuration::REDIS_KEY,$config->getKey().RT_Configuration::REDIS_KEY_DELIMITER.$config->getPublisherId(), trim($config->getValue()));
				},
				array($config)
			);
		}
	}

	/**
	 *
	 */
	public function rules()
	{
		$this->_bulkAction(function (Redis $redisConnection, array $args)
		{
			$redisConnection->del(Pool::REDIS_KEY);
			$redisConnection->del(Pool::REDIS_KEY_USER_DEFINED);
		}, array());

		$rules = Rule::loadAll(true);
		$rulesTree = $this->_getRulesTree($rules);

		$pairs = array();
		$userDefinedPairs = array();
		foreach($rules as $rule)
		{
			$ruleJson = array(Pool::KEY_PRIORITY => $rule->getPriority(), Pool::KEY_LANDING_PAGE => array(), Pool::KEY_BANNER => array());
			$ruleJson[Pool::KEY_LANDING_PAGE] = $rulesTree[$rule->getAutoIncrementId()];

			$redisKey = Pool::getRedisKey($rule->getCountryCode(), $rule->getLanguage(), $rule->getOsId(), $rule->getBrowserId(), $rule->getPublisherId());

			if($rule->isUserDefined())
			{
				$userDefinedPairs[$redisKey] = json_encode($ruleJson, JSON_FORCE_OBJECT);
			}
			else
			{
				$pairs[$redisKey] = json_encode($ruleJson, JSON_FORCE_OBJECT);
			}
		}

		$this->_bulkAction(
			function (Redis $redisConnection, array $args)
			{
				list($pairs,$userDefinedPairs) = $args;
				$redisConnection->hMset(Pool::REDIS_KEY, $pairs);
				$redisConnection->hMset(Pool::REDIS_KEY_USER_DEFINED, $userDefinedPairs);
			},
			array($pairs,$userDefinedPairs)
		);
	}

	/**
	 *
	 */
	public function downloadMethodRules()
	{
		// clear previous
		$this->_bulkAction(function (Redis $redisConnection) { $redisConnection->del(RT_DownloadMethodRule::REDIS_KEY); },array());

		$downloadMethodRules = DownloadMethodRule::loadAll(true);
		foreach($downloadMethodRules as $rule)
		{
			$this->_bulkAction(
				function (Redis $redisConnection, array $args)
				{
					/**
					 * @var DownloadMethodRule $rule
					 */
					list($rule) = $args;
					$redisConnection->hSet(
						RT_DownloadMethodRule::REDIS_KEY,
						RT_DownloadMethodRule::getRedisKey($rule->getBrowserId(), $rule->getCountryCode(), $rule->getPublisherId()),
						implode(';', array($rule->getPriority(), $rule->getDownloadMethodId())) // => priority;download_method
					);
				},
				array($rule)
			);
		}
	}

	/**
	 *
	 */
	public function injectableSettings()
	{
		// clear previous
		$this->_bulkAction(function (Redis $redisConnection) { $redisConnection->del(RT_Injectable::REDIS_KEY); },array());

		$injectableSettings = Injectable::loadAll(true);
		foreach($injectableSettings as $injectableSetting)
		{
			$this->_bulkAction(
				function (Redis $redisConnection, array $args)
				{
					/**
					 * @var Injectable $injectableSetting
					 */
					list($injectableSetting) = $args;
					$redisConnection->hSet(
						RT_Injectable::REDIS_KEY,
						RT_Injectable::getRedisKey($injectableSetting->getCountryCode(), $injectableSetting->getLanguage(), $injectableSetting->getOsId(), $injectableSetting->getBrowserId(), $injectableSetting->getPublisherId()),
						$injectableSetting->getValue()
					);
				},
				array($injectableSetting)
			);
		}
	}

	private function _lock()
	{
		foreach($this->_connections as $redisConnection)
		{
			$redisConnection->set(static::REDIS_KEY_LOCK,1);
		}
	}

	private function _unlock()
	{
		foreach($this->_connections as $redisConnection)
		{
			$redisConnection->del(static::REDIS_KEY_LOCK);
		}
	}

	public function run()
	{
		$this->_lock();
		foreach($this->_queue as $turn)
		{
			list($action,$redisConnection,$args) = $turn;
			$action($redisConnection,$args);
		}
		$this->_unlock();
	}

	/**
	 * @param callable $action
	 * @param array $args
	 */
	protected function _bulkAction(callable $action, array $args)
	{
		foreach($this->_connections as $redisConnection)
		{
			$this->_queue[] = array($action,$redisConnection,$args);
		}
	}

	/**
	 * @param Rule[] $rules
	 * @return LandingPageRule[]
	 */
	protected function _getLandingPageRules(array $rules)
	{
		$ruleIds = array();
		foreach($rules as $rule) { $ruleIds[] = $rule->getAutoIncrementId(); }
		return Rule::getLandingPageRulesByIds($ruleIds, true);
	}

	/**
	 * @param Rule[] $rules
	 * @return array
	 */
	protected function _getRulesTree(array $rules)
	{
		$rulesTree = array();
		$landingPageRules = $this->_getLandingPageRules($rules);
		foreach($landingPageRules as $landingPageRule)
		{
			$ruleId = $landingPageRule->getRuleId();
			if(!isset($rulesTree[$ruleId]))
			{
				$rulesTree[$ruleId] = array();
			}
			$rulesTree[$ruleId][$landingPageRule->getLandingPageId()] = $landingPageRule->getValue();
		}
		return $rulesTree;
	}
}