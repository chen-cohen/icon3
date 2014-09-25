<?php

namespace LPO\Digest\Injectable;

use LPO\Digest\Injectable\Configurable\AfterClick;
use LPO\Digest\Injectable\Configurable\AreYouSure;
use LPO\Digest\Injectable\Configurable\WotBlocker;
use LPO\Digest\Injectable\Configurable\GoogleAnalytics;

class Collection {

	/**
	 * @var array[]
	 */
	protected $_injectableObjects = array();

	/**
	 * @return Collection
	 */
	public static function factory()
	{
		$instance = new self();

		$instance->_injectableObjects[] = array(new DownloadMethod,'body');
		$instance->_injectableObjects[] = array(new GoogleAnalytics,'body');
		$instance->_injectableObjects[] = array(new McTorrentCheckBox,'body');

		/* configurable */
		$instance->_injectableObjects[] = array(new WotBlocker,'head');
		$instance->_injectableObjects[] = array(new AfterClick,'body');
		$instance->_injectableObjects[] = array(new AreYouSure,'head');

		$instance->_injectableObjects[] = array(new Token,'head');

		return $instance;
	}

	/**
	 * @return array|[JavascriptInjector,string][]
	 */
	public function getInjectableObjects()
	{
		return $this->_injectableObjects;
	}
}