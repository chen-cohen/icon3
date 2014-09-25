<?php

namespace LPO\Digest\Injectable;

use JsonSerializable;
use LPO\Bootstrap;
use LPO\DownloadUrl;
use Util;

class Token implements JavascriptInjector,JsonSerializable {

	const VARIABLE_NAME = 'token';

	public $staticMediaPath;
	public $directDownloadLink;
	public $downloadLink;
	public $iframeLink;
	public $ref;
	public $ua;
	public $brand;
	public $style;
	public $filename;
	public $filenameText;
	public $autoDownload;

	/**
	 * @param Bootstrap $bootstrap
	 */
	protected function _fill(Bootstrap $bootstrap)
	{
		$filename = $bootstrap->filename;
		$brand    = $bootstrap->brand;

		$this->brand = array(
			'name'  => $brand->getName(),
			'text'  => $brand->getText(),
			'links' => $brand->getLinks()
		);

		$this->ua                 = $bootstrap->ua;
		$this->ref                = $bootstrap->ref;
		$this->filename           = $filename;
		$this->filenameText       = ($filename == Bootstrap::GET_PARAM_FILENAME_DEFAULT_VALUE ? Bootstrap::MESSAGE_FILENAME_DEFAULT_VALUE : null);
		$this->iframeLink         = $bootstrap->iframeLink;
		$this->autoDownload       = $bootstrap->autoDownload;
		$this->downloadLink       = $bootstrap->downloadLink;
		$this->staticMediaPath    = $bootstrap->staticMediaPath;
		$this->directDownloadLink = ($bootstrap->downloadRedirectUrl .= '&'.DownloadUrl::GET_PARAM_EXTERNAL_ID.'='.$bootstrap->external_id->getId());
	}

	/**
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this;
	}

	/**
	 * @return string
	 */
	function getInjectedScript()
	{
		return 'var __tokens = <?php echo json_encode($'.self::VARIABLE_NAME.') ?>;';
	}

	/**
	 * @return string
	 */
	public static function getVariableName()
	{
		return self::VARIABLE_NAME;
	}

	/**
	 * @param Bootstrap $bootstrap
	 *
	 * @return mixed
	 */
	public function getTemplateVariableData(Bootstrap $bootstrap)
	{
		$this->_fill($bootstrap);
		return $this;
	}
}