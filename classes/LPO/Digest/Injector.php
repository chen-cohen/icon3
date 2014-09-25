<?php

namespace LPO\Digest;

use DOMAttr;
use DOMDocument;
use DOMXPath;
use LPO\Digest\Injectable\Collection;
use LPO\Digest\Injectable\DownloadMethod;
use LPO\Digest\Injectable\JavascriptInjector;
use LPO\Digest\Parser\Html as ParserHtml;

class Injector {

	const INDEX_FINAL        = 'index.inc';
	const RENAMED_INDEX_HTML = '.index-html';
	const STATIC_MEDIA_PATH = '###staticMediaPath###';

	/**
	 * @param string $filePath
	 * @param string $extractedDestination
	 */
	public function injectAndSaveAsHtml($filePath, $extractedDestination)
	{
		$document = $this->_parseHtml($filePath);
		$this->_appendInjectableObjectsToDocument($document);
		$this->_handleURLs($document);
		$this->_saveAsHTML($document, $extractedDestination, $filePath);
	}

	/**
	 * @param DOMDocument $document
	 */
	protected function _appendInjectableObjectsToDocument(DOMDocument $document)
	{
		$injectableObjects = Collection::factory()->getInjectableObjects();
		foreach($injectableObjects as $injectable)
		{
			list($injectableObject,$targetTag) = $injectable;
			$this->_injectScriptContent($document, $injectableObject, $targetTag);
		}
	}

	/**
	 * @param DOMDocument $document
	 * @param JavascriptInjector $injectable
	 * @param string $targetTag
	 * @return DOMDocument
	 */
	protected function _injectScriptContent(DOMDocument $document,JavascriptInjector $injectable,$targetTag)
	{
		$targetCollection = $document->getElementsByTagName($targetTag);
		$targetNode       = $targetCollection->item(0);
		$tokenScript      = $document->createElement('script', $injectable->getInjectedScript());

		$tokenScript->setAttribute('type', 'text/javascript');
		$targetNode->appendChild($tokenScript);

		return $document;
	}

	/**
	 * @param DOMDocument $document
	 * @return DOMDocument
	 */
	protected function _handleURLs(DOMDocument $document)
	{
		$xPath = new DOMXPath($document);
		$nodesWithURLs = $xPath->query('//@href|//@src');
		/**
		 * @var $attribute DOMAttr
		 */
		foreach ($nodesWithURLs as $attribute)
		{
			$node = $attribute->ownerElement;
			$value = (string)$attribute->value;
			if(
				// has .dlink class
				($node->hasAttribute('class') && strpos($node->getAttribute('class'),'dlink')!==false)
				// is inline javascript:....
				|| strpos($value,'javascript:') === 0
				// is an http(s) link
				|| preg_match('/^(?:http(?:s)?:)?\/\//',$value)
			)
			{
				continue;
			}

			// remove leading /
			if(!empty($value) && $value[0] == '/')
			{
				$value = substr($value,1);
			}
			$attribute = $attribute->name;
			$node->setAttribute($attribute, self::STATIC_MEDIA_PATH.$value);

		}
		return $document;
	}

	/**
	 * @param string $filePath
	 * @return DOMDocument
	 */
	protected function _parseHtml($filePath)
	{
		$htmlParser = new ParserHtml();
		$document = $htmlParser->parseFile($filePath);
		$document->formatOutput = true;
		return $document;
	}

	/**
	 * @param DOMDocument $document
	 * @param string $extractedDestination
	 * @param string $indexFile
	 */
	protected function _saveAsHTML(DOMDocument $document, $extractedDestination, $indexFile)
	{
		// save as index.inc
		$document->saveHTMLFile($extractedDestination . DS . static::INDEX_FINAL);
		// rename index.html into .index-html
		rename($indexFile, $extractedDestination . DS . static::RENAMED_INDEX_HTML);
	}
}