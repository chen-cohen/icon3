<?php

use LPO\Persistence\DownloadMethodRule;

require_once(__DIR__.'/JsonOutput.php');

class DownloadMethodRuleController extends JsonOutputController {

	public function init()
	{
		Yaf_Dispatcher::getInstance()->disableView();
	}

	public function indexAction(){}

	public function listAction()
	{
		$jsonArray = array();
		$downloadMethodRules = DownloadMethodRule::loadAll();
		foreach($downloadMethodRules as $downloadMethodRule)
		{
			$jsonArray[] = array(
				$downloadMethodRule->getDownloadMethodId(),
				$downloadMethodRule->getPriority(),
				$downloadMethodRule->getBrowserId(),
				$downloadMethodRule->getCountryCode(),
				$downloadMethodRule->getPublisherId(),
			);
		}

		return $this->outputJson($jsonArray);
	}
}