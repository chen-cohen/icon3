<?php

/**
 * @method Yaf_Request_Http getRequest()
 * @method Yaf_Response_Http getResponse()
 */
class JsonOutputController extends Yaf_Controller_Abstract {

	/**
	 * @param array $json
	 *
	 * @param int $jsonFlags
	 *
	 * @return bool
	 */
	public function outputJson(array $json,$jsonFlags = JSON_FORCE_OBJECT)
	{
		$response = $this->getResponse();
//		$response->setHeader('Content-Type','application/json');
		header('Content-Type: application/json');
		return $response->setBody(json_encode($json,$jsonFlags));
	}

	/**
	 * @param string $message
	 * @param int $errorStatus
	 * @return bool
	 */
	protected function outputError($message,$errorStatus = 500)
	{
		$response = $this->getResponse();
//		$response->setHeader('Content-Type','application/json');
		Util::sendStatusCodeHeader($errorStatus);
		header('Content-Type: application/json');
		return $response->setBody(json_encode(array('error'=>$message)));
	}
	/**
	 * @param string $message
	 * @param int $successStatus
	 * @return bool
	 */
	protected function outputSuccess($message,$successStatus = 200)
	{
		$response = $this->getResponse();
//		$response->setHeader('Content-Type','application/json');
		Util::sendStatusCodeHeader($successStatus);
		header('Content-Type: application/json');
		return $response->setBody(json_encode(array('success'=>$message)));
	}

	/**
	 * @param string[] $methods all caps method types. <br/><b>Example:</b> array('PUT','POST','GET','DELETE','OPTIONS')
	 * @return bool
	 */
	protected function _inAllowedHttpMethods(array $methods)
	{
		return in_array($this->getRequest()->getMethod(),$methods);
	}
}