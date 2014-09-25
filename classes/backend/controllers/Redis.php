<?php

use LPO\Db\Redis\ProtocolGenerator;
use LPO\Persistence\Synchronizer;

class RedisController extends Yaf_Controller_Abstract {

	public function init()
	{
		Yaf_Dispatcher::getInstance()->disableView();
	}

	public function indexAction(){}

	public function syncAction()
	{
		/**
		 * @var Yaf_Response_Http $response
		 */
		$response = $this->getResponse();

		$protocolGenerator = new ProtocolGenerator();
		$connections = array($protocolGenerator);

		$sync = new Synchronizer($connections);
		if($sync->isOptimizationRunning())
		{
			return $response->setBody('false');
		}

		$sync->rules();
		$sync->brands();
		$sync->publisherTypes();
		$sync->publishers();
		$sync->expiredDomains();
		$sync->configurations();
		$sync->injectableSettings();
		$sync->downloadMethodRules();
		$sync->run();

		header('Content-Type: text/plain');
		return $response->setBody($protocolGenerator->getProtocolStr());
	}
}