<?php

use LPO\Persistence\Configuration;
use LPO\Persistence\LandingPageRule;
use LPO\Persistence\Publisher;
use LPO\Persistence\PublisherLandingPage;
use LPO\Persistence\PublisherType;
use LPO\Persistence\Rule;

require_once(__DIR__.'/JsonOutput.php');

class PublisherController extends JsonOutputController {

	public function init()
	{
		Yaf_Dispatcher::getInstance()->disableView();
	}

	public function indexAction(){ }

	public function configurationAction()
	{
		$response = $this->getResponse();
		$id       = $this->getRequest()->getQuery('id');

		if(!is_numeric($id))
		{
			return $response->setBody('false');
		}

		if(!($configs = Configuration::loadMultipleBy('publisher_id',[$id],true)))
		{
			return $this->outputJson(array(),0);
		}

		$jsonArray = array();
		foreach($configs as $conf)
		{
			$jsonArray[$conf->getKey()] = array(
				'title' => $conf->getDescription(),
				'value' => $conf->getValue()
			);
		}

		return $this->outputJson($jsonArray);
	}

	public function listTypesAction()
	{
		$jsonArray = array();

		$publisherTypes = PublisherType::loadAll(true);
		foreach($publisherTypes as $publisherType)
		{
			$jsonArray[$publisherType->getId()] = $publisherType->getName();
		}

		return $this->outputJson($jsonArray);
	}

	public function listAction()
	{
		$jsonArray = array();

		$publishers = Publisher::loadAll(true);
		foreach($publishers as $publisher)
		{
			if($publisher->getId() == \LPO\Publisher::DEFAULT_ID)
			{
				continue;
			}
			$jsonArray[$publisher->getId()] = array($publisher->getId(), $publisher->getName(), $publisher->getPath(),$publisher->getTypeId());
		}

		return $this->outputJson($jsonArray);
	}

	public function publisherRulesAction()
	{
		$response = $this->getResponse();
		$id       = $this->getRequest()->getQuery('id');

		if(!$id)
		{
			return $response->setBody('false');
		}

		$jsonArray = array();

		if(!($publisher = Publisher::loadBy('id', (int)$id)))
		{
			return $response->setBody('false');
		}

		if(!($rules = $publisher->getRelatedRules()))
		{
			return $response->setBody('false');
		}

		foreach($rules as $rule)
		{
			$values = $rule->getRelatedLandingPageRules();
			if(!$values)
			{
				return $response->setBody('false');
			}
			foreach($values as $value)
			{
				$jsonArray[] = array(
					'os_id'        => $rule->getOsId(),
					'language'     => $rule->getLanguage(),
					'priority'     => $rule->getPriority(),
					'browser_id'   => $rule->getBrowserId(),
					'country_code' => $rule->getCountryCode(),
					'value'        => $value->getValue(),
					'lp_id'        => $value->getLandingPageId(),
					'rule_id'      => $rule->getAutoIncrementId(),
				);
			}
		}

		return $this->outputJson($jsonArray);
	}

	public function publisherLpIdsAction()
	{
		$id       = $this->getRequest()->getQuery('id');
		$response = $this->getResponse();

		if(!$id)
		{
			return $response->setBody('false');
		}

		$jsonArray = array();

		$publisher = Publisher::loadBy('id', (int)$id);
		$lpIds     = $publisher->getRelatedLandingPageIds();
		foreach($lpIds as $lpId)
		{
			$jsonArray[$lpId] = $lpId;
		}

		return $this->outputJson($jsonArray);
	}

	public function createAction()
	{
		$response = $this->getResponse();
		$data     = $this->getRequest()->getPost('data');
		if(!$data)
		{
			return $response->setBody('error;no data was provided');
		}

		$data = json_decode($data, true);
		$publisher = Publisher::factory($data);
		try
		{
			$publisher->create();
		} catch(PDOException $e)
		{
			return $response->setBody('error;one of the following fields is already assigned for another publisher: id,name,path,alias');
		}
		$publisher->updateLandingPages();

		return $response->setBody('true');
	}


	public function updateAction()
	{
		$response = $this->getResponse();
		$data     = $this->getRequest()->getPost('data');
		if(!$data)
		{
			return $response->setBody('false');
		}
		$data = json_decode($data, true);
		$publisher = Publisher::factory($data,true);
		$configs = $data['configuration'];
		if (!is_array($configs))
		{
			$this->outputError('there is no configuration array as expected in '.print_r($data,true));
			return false;
		}

		Configuration::deleteMultipleBy('publisher_id',[$publisher->getId()]);
		foreach($configs as $key => $value)
		{
			if(Configuration::getKeyDescription($key) == null)
			{
				$this->outputError('key out of range');
				return false;
			}
			if(!empty($value)) {
				$configuration = Configuration::factory(array(
					'key' => trim($key),
					'value' => trim($value),
					'description' => Configuration::getKeyDescription($key),
					'publisher_id' => $publisher->getId(),
					'updated_at' => null
				));
				$configuration->update();
			}
		}
		PublisherLandingPage::deleteAllByPublisherId($publisher->getId());
		$publisher->update();
		$publisher->updateLandingPages();
		return $response->setBody('true');

	}

	public function deleteAction()
	{
		$response = $this->getResponse();
		$id       = $this->getRequest()->getPost('id');

		if(!$id)
		{
			return $response->setBody('false');
		}
		$publisher = Publisher::loadBy('id', (int)$id);
		$publisher->delete();

		return $response->setBody('true');
	}

	public function deleteRuleAction()
	{
		$response = $this->getResponse();
		$id       = $this->getRequest()->getPost('id');

		if(!$id)
		{
			return $response->setBody('false');
		}
		$rule = Rule::loadBy('id', (int)$id);
		$rule->delete();

		return $response->setBody('true');
	}

	public function ruleAction()
	{
		$response = $this->getResponse();
		$params = $this->getRequest()->getPost();

		if(!isset(
			$params['country_code'],
			$params['language'],
			$params['os_id'],
			$params['browser_id'],
			$params['publisher_id'],
			$params['amount'],
			$params['priority'],
			$params['user_defined']
		))
		{
			return $response->setBody('false');
		}

		$values = & $params['amount'];

		$rule = Rule::factory($params,true);
		if($rule->exists())
		{
			$ruleId = $rule->getAutoIncrementId();
			$rule->update();
			LandingPageRule::deleteAllByRuleId($ruleId);
		}
		else
		{
			$ruleId = $rule->create();
		}

		foreach($values as $landingPageId => $value)
		{
			$landingPageRule = LandingPageRule::factory(array('landing_page_id' => $landingPageId, 'rule_id' => $ruleId, 'value' => $value));
			$landingPageRule->create();
		}

		return $response->setBody('true');
	}

	public function getAction()
	{
		$id = $this->getRequest()->getQuery('id');
		if(!$id && $id != \LPO\Publisher::DEFAULT_ID)
		{
			return $this->getResponse()->setBody('false');
		}
		$publisher = Publisher::loadBy('id', (int)$id);
		$jsonArray = array(
			'id'       => $publisher->getId(),
			'type_id'     => $publisher->getTypeId(),
			'name'     => $publisher->getName(),
			'path'     => $publisher->getPath(),
			'alias'    => $publisher->getAlias(),
			'ua'       => $publisher->getUA(),
			'brand_id' => $publisher->getBrandId(),
			'landing_page_ids' => array_flip($publisher->getRelatedLandingPageIds()),
			'configuration' => ($publisher->getConfigurations())
		);

		return $this->outputJson($jsonArray,0);
	}

	public function getpoolAction()
	{
		$params = $this->getRequest()->getQuery();
		if(!isset(
			$params['country_code'],
			$params['language'],
			$params['os_id'],
			$params['browser_id'],
			$params['publisher_id']
		))
		{
			return $this->getResponse()->setBody('false');
		}

		$jsonArray = array();

		$publisherId = $params['publisher_id'];

		$publisher = (is_numeric($publisherId)) ? new \LPO\Publisher($publisherId) : \LPO\Publisher::loadByPath($publisherId);
		$attributes = new \LPO\Attributes($publisher->getId(),$params['browser_id'],$params['country_code'],$params['language'],$params['os_id']);
		$pool = new \LPO\Pool($attributes);
		$results = $pool->getChosenRule();
		if(!$results)
		{
			$publisher->load();
			$results = $publisher->getLandingPageIds();
			$numberOfPages = count($results);
			if($numberOfPages === 0)
			{
				return $this->outputError('no pages were configured for this publisher!');
			}

			$percent = \LPO\Pool::MAX_SUM/$numberOfPages;
			foreach($results as $landingPageId)
			{
				$jsonArray[$landingPageId] = $percent;
			}
		}
		else
		{
			$jsonArray = $results[\LPO\Pool::KEY_LANDING_PAGE];
		}

		return $this->outputJson($jsonArray,JSON_FORCE_OBJECT);
	}
}