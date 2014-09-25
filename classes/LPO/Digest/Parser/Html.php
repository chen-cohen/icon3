<?php


namespace LPO\Digest\Parser;

use DOMDocument;
use ErrorException;

class Html {

	/**
	 * @param string $filePath
	 * @throws ErrorException
	 * @return DOMDocument
	 */
	public function parseFile($filePath)
	{
		$parser = new DOMDocument();
		$parser->loadHTMLFile($filePath,LIBXML_NOCDATA);
		return $parser;
	}

	/**
	 * @param string $str
	 * @throws ErrorException
	 * @return DOMDocument
	 */
	public function parseString($str)
	{
		$parser = new DOMDocument();
		$parser->loadHTML($str,LIBXML_NOCDATA);
		return $parser;
	}
}