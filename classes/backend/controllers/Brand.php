<?php

use LPO\Persistence\Brand;

require_once(__DIR__.'/JsonOutput.php');

class BrandController extends JsonOutputController {

	public function init()
	{
		Yaf_Dispatcher::getInstance()->disableView();
	}

	public function indexAction(){}

	public function listAction()
	{
		$jsonArray = array();

		$brands = Brand::loadAll();
		foreach($brands as $brand)
		{
			if($brand->getId() == 0){continue;}
			$jsonArray[$brand->getId()] = array('name'=>$brand->getName(),'links'=>$brand->getLinks(),'text'=>$brand->getText());
		}

		return $this->outputJson($jsonArray);
	}

	public function checkAction()
	{
		$name = $this->getRequest()->getQuery('name');
		if(!$name)
		{
			return $this->getResponse()->setBody('false; probably a bug, no name was provided!');
		}

		if($brand = Brand::loadBy('name',trim($name)))
		{
			return $this->getResponse()->setBody('false; brand name already taken!');
		}
		else
		{
			return $this->getResponse()->setBody('true');
		}
	}

	public function createAction()
	{
		$request = $this->getRequest();
		$params = $request->getPost();
		if(!isset($params['name'],$params['text'],$params['links']))
		{
			return $this->getResponse()->setBody('false; probably a bug, no data was provided!');
		}

		$name = trim($params['name']);
		$newBrand = Brand::factory(array('id'=>0,'name'=>$name,'text'=>$params['text'],'links'=>$params['links']));
		$newBrand->create();

		return $this->getResponse()->setBody('true');
	}
}