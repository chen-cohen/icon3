<?php

class Util {

	/**
	 * @param string $ip
	 *
	 * @return int
	 */
	public static function getSignedIntFromIp($ip)
	{
		$int = ip2long($ip);
		// is 64bit?
		return (PHP_INT_SIZE == 8) ? $int - 2147483647 : $int;
	}

	/**
	 * @param int $code
	 *
	 * @return string
	 */
	public static function translateJsonErrorCode($code)
	{
		switch((int)$code)
		{
			case JSON_ERROR_NONE            :
				return 'No errors';
				break;
			case JSON_ERROR_DEPTH            :
				return 'Maximum stack depth exceeded';
				break;
			case JSON_ERROR_STATE_MISMATCH    :
				return 'Underflow or the modes mismatch';
				break;
			case JSON_ERROR_CTRL_CHAR        :
				return 'Unexpected control character found';
				break;
			case JSON_ERROR_SYNTAX            :
				return 'Syntax error, malformed JSON';
				break;
			case JSON_ERROR_UTF8            :
				return 'Malformed UTF-8 characters, possibly incorrectly encoded';
				break;
			default                            :
				return 'Unknown error';
				break;
		}
	}

	/**
	 * @param string $destination
	 * @param int $mode
	 */
	public static function recursiveChmod($destination, $mode = 0777)
	{
		$dirIterator = new RecursiveDirectoryIterator($destination);
		$iterator    = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

		/**
		 * @var $item SplFileInfo
		 */
		foreach($iterator as $item)
		{
			switch($item->getFilename())
			{
				case '..':
				case '.':
					break;
				default:
					chmod($item->getPathname(), $mode);
			}
		}
	}

	/**
	 * @param string $directory
	 */
	public static function recursiveRemoveDir($directory)
	{
		$dirIterator = new RecursiveDirectoryIterator($directory);
		$iterator    = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST);
		/**
		 * @var $item SplFileInfo
		 */
		foreach($iterator as $item)
		{
			$pathName = $item->getPathname();
			$filename = $item->getFilename();
			if($item->isDir())
			{
				switch($filename)
				{
					case '..':
						break;
					case '.' :
						break;
					default  :
						rmdir($pathName);
				}
			}
			else
			{
				unlink($pathName);
			}
		}
		rmdir($directory);
	}

	/**
	 * @param $string
	 *
	 * @return string
	 */
	public static function camelCaseToUnderscore($string)
	{
		$matches = array();
		preg_match_all('/((?:^|[A-Z])[a-z]+)/', $string, $matches);
		foreach($matches as &$match)
		{
			$match = strtolower($match);
		}

		return implode('_', $matches);
	}

	/**
	 * @param int $statusCode
	 *
	 * @return bool
	 */
	public static function sendStatusCodeHeader($statusCode)
	{
		if(isset(static::$_status_codes[(int)$statusCode]))
		{
			header($_SERVER['SERVER_PROTOCOL'] . ' ' . $statusCode . ' ' . static::$_status_codes[$statusCode], true, $statusCode);
			return true;
		}
		return false;
	}

	/**
	 * @var array
	 */
	protected static $_status_codes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		426 => 'Upgrade Required',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended'
	);

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	private static function _hex4($string)
	{
		$result = dechex($string + 0);
		while(strlen($result) < 4)
		{
			$result = '0' . $result;
		}
		return $result;
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public static function encodeStringForJson($string)
	{
		$result = '';
		$length = strlen($string);
		$offset = 0;

		while($offset < $length)
		{
			$c = $string[$offset];
			$o = ord($c);
			if($o & 0x80)
			{
				$utfCode = 0;
				if(++$offset >= $length)
				{
					continue;
				}

				if(($o & 0xe0) == 0xc0) // 0x0080 - 0x07ff
				{
					$utfCode = ($o & 0x1f) << 6;
					$c       = $string[$offset];
					$o       = ord($c);
					if(($o & 0xc0) != 0x80 || (++$offset >= $length))
					{
						continue;
					}
					$utfCode |= ($o & 0x3f);
				}
				else if(($o & 0xf0) == 0xe0) // 0x0800 - 0xffff
				{
					$utfCode = ($o & 0x0f) << 12;
					$c       = $string[$offset];
					$o       = ord($c);
					if(($o & 0xc0) != 0x80 || (++$offset >= $length))
					{
						continue;
					}
					$utfCode |= ($o & 0x3f) << 6;
					$c = $string[$offset];
					$o = ord($c);
					if(($o & 0xc0) != 0x80 || (++$offset >= $length))
					{
						continue;
					}
					$utfCode |= ($o & 0x3f);
				}
				else // 0xffff - .... [unsupported]
				{
					while(($o & 0xc0) == 0x80)
					{
						if(++$offset >= $length)
						{
							break;
						}
						$c = $string[$offset];
						$o = ord($c);
					}
				}

				if($utfCode)
				{
					$result .= '\u';
					$result .= static::_hex4($utfCode);
				}

			}
			else
			{
				switch($c)
				{
					case '"':
						$result .= '\"';
						break;
					case "\\":
						$result .= '\\\\';
						break;
					case "\n":
						$result .= '\\n';
						break;
					case "\r":
						$result .= '\\r';
						break;
					case "\t":
						$result .= '\\t';
						break;
					default:
						if($o <= 0x1f)
						{
							$result .= '\u';
							$result .= static::_hex4($o);
						}
						else
						{
							$result .= $c;
						}
						break;
				}
				$offset++;
			}
		}

		return $result;
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public static function sanitizeStringForJson($string)
	{
		$result = '';
		$length = strlen($string);
		$offset = 0;

		while($offset < $length)
		{
			$c = $string[$offset];
			$o = ord($c);
			$sequence = "";

			if($o & 0x80)
			{
				$utfCode = 0;
				if(++$offset >= $length)
				{
					continue;
				}

				$sequence .= $c;
				if(($o & 0xe0) == 0xc0) // 0x0080 - 0x07ff
				{
					$utfCode = ($o & 0x1f) << 6;
					$c       = $string[$offset]; $sequence .= $c;
					$o       = ord($c);
					if(($o & 0xc0) != 0x80 || (++$offset >= $length))
					{
						continue;
					}
					$utfCode |= ($o & 0x3f);
				}
				else if(($o & 0xf0) == 0xe0) // 0x0800 - 0xffff
				{
					$utfCode = ($o & 0x0f) << 12;
					$c       = $string[$offset]; $sequence .= $c;
					$o       = ord($c);
					if(($o & 0xc0) != 0x80 || (++$offset >= $length))
					{
						continue;
					}
					$utfCode |= ($o & 0x3f) << 6;
					$c = $string[$offset]; $sequence .= $c;
					$o = ord($c);
					if(($o & 0xc0) != 0x80 || (++$offset >= $length))
					{
						continue;
					}
					$utfCode |= ($o & 0x3f);
				}
				else // 0xffff - .... [unsupported]
				{
					while(($o & 0xc0) == 0x80)
					{
						if(++$offset >= $length)
						{
							break;
						}
						$c = $string[$offset];
						$o = ord($c);
					}

				}

				if($utfCode)
				{
					$result .= $sequence;
				}
			}
			else
			{
				switch($c)
				{
					case '"':
					case "\\":
					case "\n":
					case "\r":
					case "\t":
						$result .= $c;
						break;
					default:
						if($o > 0x1f) { $result .= $c; }
						break;
				}
				$offset++;
			}
		}

		return $result;
	}

	/**
	 * @param string $input
	 * @return string
	 */
	public static function removeInvalidUTF8Chars($input)
	{
		$input = preg_replace(
			'/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]'.
			'|[\x00-\x7F][\x80-\xBF]+'.
			'|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
			'|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})'.
			'|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
			'', $input );

		//reject overly long 3 byte sequences and UTF-16 surrogates and replace with ?
		return preg_replace(
			'/\xE0[\x80-\x9F][\x80-\xBF]'.
			'|\xED[\xA0-\xBF][\x80-\xBF]/S',
			'', $input );
	}

	/**
	 * @param $url
	 *
	 * @return string
	 */
	public static function concatenateQueryStringToUrl($url)
	{
		if(isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
		{
			$data = QueryString::getInstance()->getData();
			$url .= '&' . http_build_query($data);
			return $url;
		}
		return $url;
	}
}