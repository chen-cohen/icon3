<?php

use LPO\Persistence\Configuration;

require_once(__DIR__.'/JsonOutput.php');

class ConfigController extends JsonOutputController {

	public function init()
	{
		Yaf_Dispatcher::getInstance()->disableView();
	}

	public function indexAction(){}

	public function listAction()
	{
		$jsonArray = array();

		$configs = Configuration::loadAllGlobals();

		foreach($configs as $conf)
		{
			$jsonArray[$conf->getKey()] = array(
				'title' => $conf->getDescription(),
				'value' => $conf->getValue(),
				'updated_at' => $conf->getUpdatedAt()
			);
		}

		return $this->outputJson($jsonArray);
	}

	public function keysListAction()
	{
		$keys = Configuration::getAllKeys();
		return $this->outputJson($keys);
	}


	public function setAction()
	{
		$request = $this->getRequest();
		$key     = $request->getPost('key');
		if(!$key)
		{
			return $this->getResponse()->setBody('false; probably a bug, no key was provided!');
		}

		$key   = trim($key);
		$value = $request->getPost($key);

		if(strpos('/', trim($value)) === false)
		{
			if($conf = Configuration::loadBy('key', $key))
			{
				$conf->setValue($value);
				if($conf->update())
				{
					return $this->getResponse()->setBody('true');
				}
			}
		}
		else
		{
			return $this->getResponse()->setBody('false; invalid domain provided!');
		}
		return $this->getResponse()->setBody('false; probably a bug, key does not exist!');
	}
}