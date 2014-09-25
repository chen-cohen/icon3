<?php

use LPO\Persistence\Configuration;
use LPO\Persistence\Publisher;

require_once(__DIR__.'/JsonOutput.php');

class DownloadDomainController extends JsonOutputController {

	public function init()
	{
		Yaf_Dispatcher::getInstance()->disableView();
	}

	/**
	 * @return $this|bool
	 */
	public function indexAction()
	{
		if(!$this->_inAllowedHttpMethods(['GET']))
		{
			return $this->outputError('Method not implemented',501);
		}

		$request = $this->getRequest();
		switch($request->getMethod())
		{
			case 'GET':
				return $this->_list();
				break;
		}
		return $this;
	}

	private function _deleteMultiple()
	{
		$ids = explode(',',$this->getRequest()->getParam('id'));
		try
		{
			$numberOfDeletedItems = Configuration::deleteMultipleByKeyAndPublisher(Configuration::KEY_DOWNLOAD_DOMAIN, $ids);
			return $this->outputSuccess($numberOfDeletedItems.' records deleted successfully');
		}
		catch(Exception $e)
		{
			return $this->outputError('record deletion failed');
		}
	}

	protected function _delete()
	{
		$publisherId = $this->getRequest()->getParam('id');
		if(!isset($publisherId))
		{
			return $this->outputError('publisher_id parameter was not provided',400);
		}

		if(strpos($publisherId,',')!==false)
		{
			return $this->_deleteMultiple();
		}

		if(!(is_numeric($publisherId) && $publisherId == (int)$publisherId))
		{
			return $this->outputError('publisher_id parameter must be an integer',400);
		}

		$downloadDomain = Configuration::loadByKeyAndPublisher(Configuration::KEY_DOWNLOAD_DOMAIN,(int)$publisherId);
		if(!$downloadDomain)
		{
			return $this->outputError('matching record not found',416);
		}

		$successful = $downloadDomain->delete();

		return ($successful ? $this->outputSuccess('record deleted successfully') : $this->outputError('record was not deleted',500));
	}

	/**
	 * @return bool
	 */
	protected function _list()
	{
		$jsonArray = array();
		$downloadDomains = Configuration::loadMultipleBy('`key`',['"'.Configuration::KEY_DOWNLOAD_DOMAIN.'"']);

		foreach($downloadDomains as $downloadDomain)
		{
			$jsonArray[] = ['domain'=>$downloadDomain->getValue(),'publisher_id'=>$downloadDomain->getPublisherId()];
		}

		return $this->outputJson($jsonArray,0);
	}

	/**
	 * @return $this|bool
	 */
	public function publisherAction()
	{
		if(!$this->_inAllowedHttpMethods(['POST','GET','DELETE']))
		{
			return $this->outputError('Method not implemented',501);
		}

		switch($this->getRequest()->getMethod())
		{
			case 'GET':
				return $this->_list();
				break;
			case 'DELETE':
				return $this->_delete();
				break;
			case 'POST':
				return $this->_attachPublisher();
		}

		return $this;
	}

	protected function _attachPublisher()
	{
		$input =  (isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : $this->getRequest()->getPost('data'));

		//todo: make it a bit more generic
		if(!$input)
		{
			return $this->outputError('No input was provided',400);
		}
		if(!($data = json_decode($input)))
		{
			return $this->outputError('Malformed JSON provided', 422);
		}
		if(!is_array($data))
		{
			return $this->outputError('please provide an array of downloadDomain and publisherId pairs', 400);
		}

		$publishers = array();
		foreach($data as $index=>$pair)
		{
			if(!isset($pair->downloadDomain, $pair->publisherId))
			{
				return $this->outputError('wrong pair format at index #'.$index.' , expecting: {"downloadDomain":"<string>","publisherId":<int>}', 400);
			}

			$publisherId = (int)$pair->publisherId;
			if($publisher = Publisher::loadBy('id', $publisherId,true))
			{
				$publishers[$publisherId] = $publisher;
			}
			else
			{
				return $this->outputError('invalid publisherId provided: '.$publisherId, 416);
			}
		}

		foreach($data as $pair)
		{
			/**
			 * @var Publisher $publisher
			 */
			$publisher = $publishers[(int)$pair->publisherId];
			$publisher->updateDownloadDomain($pair->downloadDomain);
		}

		return $this->outputSuccess('records successfully updated');
	}
}