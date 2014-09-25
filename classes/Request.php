<?php
class Request {

	const IP_REGEX = '/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/';
	/**
	 *
	 */
	const OS_DEFAULT 	= 201;
	const OS_BLACKBERRY = 202;
	const OS_SYMBIAN    = 203;
	const OS_xNOKIAx    = 204;

	const OS_MAC_OSX 	= 301;
	const OS_IOS	 	= 302;

	const OS_LINUX 		= 401;
	const OS_ANDROID 	= 402;

	const OS_WIN_XP 	= 501;
	const OS_WIN_XP_x64 = 502;
	const OS_WIN_VISTA 	= 600;
	const OS_WIN_7 		= 601;
	const OS_WIN_8 		= 602;
	const OS_WIN_8_1 	= 603;

	/**
	 *
	 */
	const BROWSER_DEFAULT	= 0;
	const BROWSER_IE 		= 1;
	const BROWSER_FIREFOX	= 2;
	const BROWSER_CHROME 	= 4;
	const BROWSER_OPERA		= 8;
	const BROWSER_CHROMIUM 	= 16;
	const BROWSER_SAFARI 	= 32;

	/**
	 *
	 */
	const LANGUAGE_DEFAULT = 'O1';
	/**
	 *
	 */
	const COUNTRY_CODE_DEFAULT = 'O1';

	/**
	 * @var array
	 */
	protected static $_os_list = array
	(
		'windows nt 5.1' => self::OS_WIN_XP,
		'windows nt 5.2' => self::OS_WIN_XP_x64,
		'windows nt 6.0' => self::OS_WIN_VISTA,
		'windows nt 6.1' => self::OS_WIN_7,
		'windows nt 6.2' => self::OS_WIN_8,
		'windows nt 6.3' => self::OS_WIN_8_1,
		'ipad'           => self::OS_IOS,
		'ipod'           => self::OS_IOS,
		'iphone'         => self::OS_IOS,
		'blackberry'     => self::OS_BLACKBERRY,
		'symbian'        => self::OS_SYMBIAN,
		'nokia'          => self::OS_xNOKIAx,
		'android'        => self::OS_ANDROID,
		'mac'            => self::OS_MAC_OSX,
		'linux'          => self::OS_LINUX,
	);

	/**
	 * @var null|int
	 */
	protected static $_os = null;
	/**
	 * @var null|int
	 */
	protected static $_browser = null;
	/**
	 * @var array
	 */
	protected static $_browser_list = array
	(
		'msie'		=> self::BROWSER_IE,
		'firefox'	=> self::BROWSER_FIREFOX,
		'chrome'	=> self::BROWSER_CHROME,
		'opera'		=> self::BROWSER_OPERA,
		'chromium'	=> self::BROWSER_CHROMIUM,
		'safari'	=> self::BROWSER_SAFARI,
	);
	/**
	 * @var array
	 */
	protected static $_browser_list_regex = array
	(
		// modern MSIE uses trident >= 7.0  (i.e. MSIE > 10)
		'/trident\/(?:[7-9]|[1-9][0-9])\./i' => self::BROWSER_IE,
//		'/trident\/(?:[7-9]|[1-9][0-9])\..+rv\:([0-9\.]+)/i' => self::BROWSER_MODERN_IE, //with $1 as browser version
	);

	/**
	 * @var null|string
	 */
	static protected $_country = null;
	/**
	 * @var null|string
	 */
	static protected $_lang = null;
	/**
	 * @var null|array
	 */
	static protected $_langs = null;
	/**
	 * @var null|array
	 */
	protected static $_headers = null;
	/**
	 * @var null|string
	 */
	private static $_ip = null;

	/**
	 * @return string
	 */
	public static function getIp()
	{
		if (!is_null(static::$_ip))
		{
			return static::$_ip;
		}

		if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			list($ip) = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
			return static::$_ip = trim($ip);
		}

		$ip = (!empty($_SERVER['HTTP_CF_CONNECTING_IP']))
			? $_SERVER['HTTP_CF_CONNECTING_IP']
			: $_SERVER['REMOTE_ADDR'];

		$ip = preg_replace('/[^0-9\.]/', '', $ip);
		if (preg_match(static::IP_REGEX, $ip))
		{
			return static::$_ip = $ip;
		}

		return static::$_ip;
	}

	/**
	 * @return string|null
	 */
	public static function getHostName()
	{
		return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
	}

	/**
	 * @param null|string $url
	 * @return array
	 */
	public static function getQueryStringAsArray($url = null)
	{
		$qs = array();
		if (!$url)
		{
			$url = static::getQueryString();
		}
		parse_str(parse_url($url, PHP_URL_QUERY), $qs);
		return $qs;
	}

	/**
	 * @return string|null
	 */
	public static function getQueryString()
	{
		return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
	}

	/**
	 * @return int
	 */
	public static function getOS()
	{
		if (!is_null(static::$_os))
		{
			return static::$_os;
		}

		$userAgent = strtolower(static::getUserAgent());
		foreach (static::$_os_list as $key => $value)
		{
			if (strpos($userAgent, $key) !== false)
			{
				return (static::$_os = $value);
				break;
			}
		}
		return (static::$_os = static::OS_DEFAULT); // other
	}

	/**
	 * @return string|null
	 */
	public static function getUserAgent()
	{
		return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
	}

	/**
	 * @return int
	 */
	public static function getBrowser()
	{
		if (!is_null(static::$_browser))
		{
			return static::$_browser;
		}

		$userAgent = strtolower(static::getUserAgent());

		if(is_null($userAgent))
		{
			return (static::$_browser = static::BROWSER_DEFAULT); // unknown
		}

		foreach (static::$_browser_list as $key => $value)
		{
			if (strpos($userAgent, $key) !== false)
			{
				return (static::$_browser = $value);
				break;
			}
		}

		foreach (static::$_browser_list_regex as $regex => $value)
		{
			if (preg_match($regex,$userAgent))
			{
				return (static::$_browser = $value);
				break;
			}
		}

		return (static::$_browser = static::BROWSER_DEFAULT); // unknown
	}

	/**
	 * @return string
	 */
	public static function getCountry()
	{
		if (is_null(static::$_country))
		{
			$countryCodeByPriority = array(
				// CloudFlare
				(!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) ? $_SERVER['HTTP_CF_IPCOUNTRY'] : null,
				// GeoIp
				(!empty($_SERVER['CCODE'])) ? $_SERVER['CCODE'] : null,
				// unknown
				static::COUNTRY_CODE_DEFAULT,
			);

			foreach ($countryCodeByPriority as $value)
			{
				if (!empty($value))
				{
					static::$_country = $value;
					break;
				}
				continue;
			}
		}

		return static::$_country;
	}

	/**
	 * @return array
	 */
	public static function getAllLanguages()
	{
		if (is_null(static::$_langs))
		{
			if(empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			{
				return static::$_langs = array(static::LANGUAGE_DEFAULT);
			}
			$acceptLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			foreach ($acceptLanguages as &$lang)
			{
				$lang = explode(';', $lang);
				$lang = explode('-', $lang[0]);
				$lang = $lang[0];
			}
			static::$_langs = $acceptLanguages;
		}
		return static::$_langs;
	}


	/**
	 * @return string
	 */
	public static function getLanguage()
	{
		if (is_null(static::$_lang))
		{
			if(empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			{
				return static::$_lang = static::LANGUAGE_DEFAULT;
			}
			$acceptLanguage = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$acceptLanguage = explode('-', $acceptLanguage[0]);
			static::$_lang = $acceptLanguage[0];
		}

		return static::$_lang;
	}

	/**
	 * @return array
	 */
	public static function getHeaders()
	{
		if (is_null(static::$_headers))
		{
			$headers = array();
			foreach ($_SERVER as $name => $value)
			{
				if ($name == 'HTTP_COOKIE')
				{
					continue;
				}
				if (substr($name, 0, 5) == 'HTTP_')
				{
					$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
				}
			}
			static::$_headers = $headers;
		}
		return static::$_headers;
	}
}