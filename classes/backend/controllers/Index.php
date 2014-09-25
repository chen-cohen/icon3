<?php

class IndexController extends Yaf_Controller_Abstract {

	public function init()
	{
		Yaf_Dispatcher::getInstance()->disableView();
	}

	public function indexAction(){}
}