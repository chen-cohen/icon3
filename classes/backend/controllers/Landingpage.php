<?php

use Db\Adapter\Pdo\Dbase;
use LPO\Digest\Digester;
use LPO\Persistence\LandingPage;
use LPO\Publisher;

require_once(__DIR__.'/JsonOutput.php');

class LandingpageController extends JsonOutputController {

	public function init()
	{
		Yaf_Dispatcher::getInstance()->disableView();
	}

	public function indexAction()
	{
		$jsonArray = array();
		$landingPagePaths = Digester::getAllLandingPagePaths();
		foreach($landingPagePaths as $landingPagePath)
		{
			$explodedPath  = explode(DIRECTORY_SEPARATOR, $landingPagePath);
			$landingPageId = end($explodedPath);
			$landingPageName = $landingPageId;

			$fileNameFile    = $landingPagePath.DS.Digester::ARCHIVE_FILENAME_REFERENCE;
			if(is_readable($fileNameFile))
			{
				$landingPageName = trim(file_get_contents($fileNameFile));
			}
			$jsonArray[] = array('id'=>$landingPageId,'name'=>$landingPageName);
		}

		return $this->outputJson($jsonArray,0);
	}

	public function abortAction()
	{
		$unique   = $this->getRequest()->getPost('unique');
		$response = $this->getResponse();
		if(!$unique)
		{
			return $response->setBody('Invalid operation!');
		}

		$digester = new Digester();
		$digester->abort(trim($unique));

		return $response->setBody('true');
	}

	public function saveAction()
	{
		$unique   = $this->getRequest()->getPost('unique');
		$response = $this->getResponse();
		if(!$unique)
		{
			return $response->setBody('Invalid operation!');
		}

		$digester = new Digester();
		$trim     = trim($unique);
		$digester->save($trim);
		$landingPage = LandingPage::factory(array('id' => $trim));
		$landingPage->create();

		return $response->setBody('true');
	}

	public function uploadAction()
	{
		$request = $this->getRequest();
		$file    = $request->getFiles('archive');
		if(!$file)
		{
			return $this->getResponse()->setBody('please provide an archive file!');
		}

		$digester = new Digester();
		$unique   = $digester->digest($file['tmp_name'],$file['name']);

		$iframePath = '/api/preview?mode=tmp&id='.$unique;

		return $this->outputJson(array('unique' => $unique, 'iframePath' => $iframePath));
	}

	public function redigestAction()
	{
		$digester = new Digester();
		$digester->reDigestAll();
		return $this->outputSuccess('re-digested all pages successfully');
	}

	public function toppagesAction()
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

		$where = array('lp_id > 0','report_date > CURRENT_DATE - INTERVAL 7 day');
		if(is_numeric($params['publisher_id']) && $params['publisher_id'] != Publisher::DEFAULT_ID) { $where[] = 'publisher_id = ' . $params['publisher_id']; }
		if($params['language'] != Request::LANGUAGE_DEFAULT)                                        { $where[] = 'language = "' . $params['language'].'"'; }
		if($params['country_code'] != Request::COUNTRY_CODE_DEFAULT)                                { $where[] = 'country_code = "' . $params['country_code'].'"'; }
		if(is_numeric($params['os_id']) && $params['os_id'] != Request::OS_DEFAULT)                 { $where[] = 'os_id = ' . $params['os_id']; }
		if(is_numeric($params['browser_id']) && $params['browser_id'] != Request::BROWSER_DEFAULT)  { $where[] = 'browser_id = ' . $params['browser_id']; }

		$sql = 'SELECT lp_id AS id, sum(installer_started)/sum(lp_impression) AS ratio
				FROM lp_summary
				WHERE '.implode(' AND ',$where).'
				GROUP BY lp_id
				ORDER BY sum(installer_started)/sum(lp_impression) DESC
				LIMIT 20';

		$adapter = Dbase::getConnection();
		$query = $adapter->query($sql);
		$results = $query->fetchAll($adapter::FETCH_ASSOC);
		if(!$results)
		{
			return $this->outputError('error occurred, probably no traffic from this publisher!',500);
		}

		return $this->outputJson($results,0);
	}
}