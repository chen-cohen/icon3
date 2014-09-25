<?php

namespace LPO\Digest\Validator;

use DOMNode;
use ErrorException;
use LPO\Digest\Parser\Html;

class Css {

	const ERR_FILE_NOT_FOUND     = 1;
	const ERR_CSS_URL_FOUND      = 2;
	const ERR_HTML_PARSE_FAILED  = 3;

	/**
	 * @param string $filePath
	 * @return int|boolean - true OR integer in case of an error
	 */
	public static function validate($filePath)
	{
		try
		{
			$content = file_get_contents($filePath);
		}
		catch (ErrorException $e)
		{
			return static::ERR_FILE_NOT_FOUND;
		}

		$parser = new Html();
		$document = $parser->parseString($content);

		$styleTags = $document->getElementsByTagName('style');
		/**
		 * @var DOMNode $styleTag
		 */
		foreach($styleTags as $styleTag)
		{
			if(preg_match('/url\((?!data\:)/',$styleTag->textContent))
			{
				return static::ERR_CSS_URL_FOUND;
			}
		}

		return true;
	}
}