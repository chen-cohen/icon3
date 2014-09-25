<?php

use LPO\Attributes;
use LPO\Bootstrap;
use LPO\Digest\Digester;
use LPO\ExternalId;
use LPO\LandingPage;
use LPO\Publisher;

/**
 * @method Yaf_Request_Http getRequest()
 * @method Yaf_Response_Http getResponse()
 */
class PreviewController extends Yaf_Controller_Abstract {

	public function init()
	{
		Yaf_Dispatcher::getInstance()->disableView();
	}

	public function indexAction()
	{
		$params = $this->getRequest()->getQuery();

		if(!isset($params['id']))
		{
			return $this->getResponse()->setBody('Please provide "id" param in query string.');
		}

		$id      = $params['id'];
		$mode    = Bootstrap::MODE_NO_DOWNLOAD;
		$hasMode = isset($params['mode']);

		if($hasMode)
		{
			if($params['mode'] == 'tmp')
			{
				$mode |= Bootstrap::MODE_NO_DOWNLOAD;
				$mode |= Bootstrap::MODE_TEMPORARY;
			}
			else if($params['mode'] == 'fullscreen')
			{
				$mode |= Bootstrap::MODE_NO_SCRIPT;
			}
		}
		$lp         = new LandingPage($id);
		$externalId = new ExternalId();
		$publisher  = Publisher::loadByPath('default');
		$attributes = new Attributes($publisher->getId(),Request::BROWSER_DEFAULT,Request::COUNTRY_CODE_DEFAULT,Request::LANGUAGE_DEFAULT,Request::OS_DEFAULT);
		$bootstrap  = new Bootstrap($publisher, $attributes, $mode);

		return $this->getResponse()->setBody($bootstrap->renderLandingPage($externalId, $lp));
	}
}