<?php

namespace LPO\Digest;

use Util;
use Exception;
use ErrorException;
use UnexpectedValueException;
use LPO\Digest\Extractor\Zip as ExtractorZip;
use LPO\Digest\Validator\File as ValidatorFile;
use LPO\Digest\Validator\Php as ValidatorPhp;
use LPO\Digest\Validator\Css as ValidatorCss;
use LPO\Digest\Validator\Duplicate as ValidatorDuplicate;

class Digester {


	const INDEX_HTML 					= 'index.html';
	const PREVIEW_FILE 					= 'preview.php';
	const SCREENSHOT_FILE               = 'thumb.jpg';
	const ARCHIVE_FILENAME_REFERENCE	= '.filename';

	const TEMP_DIR                         = '/var/tmp/';
	const STATIC_LP_DIR                    = '/../lpo_landing_pages/';
	const STATIC_LP_DIR_MEDIA_PATH         = '/media/';
	const STATIC_LP_DIR_MEDIA_PATH_PREVIEW = '/pmedia/';

	const ERR_INDEX_HTML_NOT_FOUND 		= 1;
	const ERR_BLACKLISTED_FILE_FOUND 	= 2;

	/**
	 * @param string $zipFilePath
	 * @param string $filename
	 *
	 * @throws Exception
	 * @return string
	 */
	public function digest($zipFilePath,$filename)
	{
		$unique =  time();
		$extractedDestination = BASE . static::TEMP_DIR. $unique;

		try
		{
			$this->_extractZipFile($zipFilePath, $extractedDestination);
		}
		catch (Exception $e)
		{
			throw $e;
		}

		try
		{
			$indexFile = $this->_getIndexFilePath($extractedDestination);
			$this->_saveFilenameReference($filename,$extractedDestination);
			$this->_validatePhpInjections($indexFile);
			$this->_validateCssUrl($indexFile);
			$this->_injectScriptAndSaveAsHtml($indexFile,$extractedDestination);
		}
		catch (Exception $e)
		{
			Util::recursiveRemoveDir($extractedDestination);
			throw $e;
		}

		return $unique;
	}

	/**
	 * @param string $unique
	 * @throws ErrorException
	 */
	public function abort($unique)
	{
		$path = BASE . static::TEMP_DIR . $unique;
		if(!is_dir($path))
		{
			throw new ErrorException('Error while aborting, publisher not found!');
		}
		Util::recursiveRemoveDir($path);
	}

	/**
	 * @param string $unique
	 * @throws ErrorException
	 */
	public function save($unique)
	{
		$path = BASE . static::TEMP_DIR . $unique;
		if(!is_dir($path))
		{
			throw new ErrorException('Error while saving, publisher not found!');
		}

//		// remove preview.php
//		unlink($path.DS.static::PREVIEW_FILE);

		$lpStaticDir = BASE . static::STATIC_LP_DIR;
		if(!is_dir($lpStaticDir)){ mkdir($lpStaticDir);}

		// move to permanent dir
		$newPath = $lpStaticDir . $unique;
		rename($path, $newPath);

		static::_saveScreenshot($unique,$newPath);
	}

	/**
	 * @param $unique
	 * @param $path
	 */
	protected static function _saveScreenshot($unique,$path)
	{
		$cmd = sprintf('phantomjs %s "%s" %s'
			,BASE.'/etc/take_screenshot.js'
			,'http://mkt:ougGtss5XlRWu0N@localhost:3645/api/preview?id='.$unique.'&mode=fullscreen'
			,$path.'/'.static::SCREENSHOT_FILE
		);
		shell_exec($cmd);
	}

	/**
	 * @param string $zipFilePath
	 * @param string $extractedDestination
	 * @throws ErrorException
	 */
	protected function _extractZipFile($zipFilePath, $extractedDestination)
	{
		if(!is_readable($zipFilePath))
		{
			throw new ErrorException('Uploaded archive file is not readable!');
		}
		$extractor = new ExtractorZip();
		$extractor->extract($zipFilePath, $extractedDestination);
	}

	/**
	 * @param string $extractedDestination
	 * @return string|int
	 */
	protected function _validateIndexFile($extractedDestination)
	{
		$extractedDestination .= DS;
		$indexFile = $extractedDestination . static::INDEX_HTML;

		if(!file_exists($indexFile))
		{
			return static::ERR_INDEX_HTML_NOT_FOUND;
		}

		$fileValidator = new ValidatorFile();
		if(!$fileValidator->validateDir(
			$extractedDestination,
			array('html','png','jpg','gif','css','js','mp3')
			/*,array ('text/plain','text/html', 'image/png', 'image/jpg', 'image/gif', 'text/javascript' , 'text/css',)*/
		)
		)
		{
			return static::ERR_BLACKLISTED_FILE_FOUND;
		}

		return $indexFile;
	}

	/**
	 * @param string $indexFile
	 * @return bool
	 * @throws ErrorException
	 */
	protected function _validatePhpInjections($indexFile)
	{
		$phpValidated = ValidatorPhp::validate($indexFile);
		if ($phpValidated !== true)
		{
			switch ($phpValidated)
			{
				case ValidatorPhp::ERR_FILE_NOT_FOUND:
					throw new ErrorException('Invalid index file provided: file not found!');
					break;
				case ValidatorPhp::ERR_INJECTED_PHP_CODE:
					throw new ErrorException('Invalid index file provided: PHP code found!');
					break;
				default:
					throw new ErrorException('Invalid index file provided: reason unknown!');
					break;
			}
		}
		return true;
	}

	/**
	 * @param string $indexFile
	 * @return bool
	 * @throws ErrorException
	 */
	protected function _validateCssUrl($indexFile)
	{
		$cssValidated = ValidatorCss::validate($indexFile);
		if ($cssValidated !== true)
		{
			switch ($cssValidated)
			{
				case ValidatorCss::ERR_FILE_NOT_FOUND:
					throw new ErrorException('Invalid index file provided: file not found!');
					break;
				case ValidatorCss::ERR_CSS_URL_FOUND:
					throw new ErrorException('Invalid index file provided: URL addresses in <style>...</style> found!');
					break;
				default:
					throw new ErrorException('Invalid index file provided: reason unknown!');
					break;
			}
		}
		return true;
	}

	/**
	 * @param $extractedDestination
	 * @return int|string
	 * @throws \UnexpectedValueException
	 */
	protected function _getIndexFilePath($extractedDestination)
	{
		$indexFile = $this->_validateIndexFile($extractedDestination);
		if (is_int($indexFile))
		{
			switch($indexFile)
			{
				case static::ERR_INDEX_HTML_NOT_FOUND:
					throw new UnexpectedValueException('Index file not found!');
					break;
				case static::ERR_BLACKLISTED_FILE_FOUND:
					throw new UnexpectedValueException('Forbidden file was found. ');
					break;
			}

		}
		return $indexFile;
	}

	/**
	 * @param string $extractedDestination
	 */
	protected function _savePreviewFile($extractedDestination)
	{
		copy(BASE.'/etc/uploader/preview.php',$extractedDestination.DS.static::PREVIEW_FILE);
	}

	/**
	 * @param string $indexFile
	 * @param string $extractedDestination
	 */
	protected function _injectScriptAndSaveAsHtml($indexFile, $extractedDestination)
	{
		$injector = new Injector();
		$injector->injectAndSaveAsHtml($indexFile,$extractedDestination);
	}

	/**
	 * @return string[]
	 */
	public static function getAllLandingPagePaths()
	{
		return glob(BASE.static::STATIC_LP_DIR.'*', GLOB_ONLYDIR);
	}

	/**
	 *
	 */
	public function reDigestAll()
	{
		$paths = static::getAllLandingPagePaths();
		foreach($paths as $path)
		{
			$this->_reDigestLandingPage($path);
		}

		$cmd = 'sh '.BASE.DS.'etc'.DS.'redigest_landing_pages.sh';
		shell_exec($cmd);
	}

	/**
	 * @param string $path
	 */
	protected function _reDigestLandingPage($path)
	{
		$indexFile = $path.DS.Injector::RENAMED_INDEX_HTML;
		$this->_injectScriptAndSaveAsHtml($indexFile,$path);
	}

	/**
	 * @param string $filename
	 * @param string $extractedDestination
	 *
	 * @return int
	 * @throws ErrorException
	 */
	protected function _saveFilenameReference($filename, $extractedDestination)
	{
		$filename = trim($filename);
		list($filename) = explode('.',$filename);
		$this->_validateFilenameDuplication($filename);
		return file_put_contents($extractedDestination.DS.static::ARCHIVE_FILENAME_REFERENCE, $filename);
	}

	/**
	 * @param string $filename
	 *
	 * @throws ErrorException
	 */
	protected function _validateFilenameDuplication($filename)
	{
		$duplicationValidated = ValidatorDuplicate::validate($filename);
		if($duplicationValidated !== true)
		{
			switch($duplicationValidated)
			{
				case ValidatorDuplicate::ERR_FOUND_LP_NAME_DUPLICATE:
					throw new ErrorException('Duplicated archive provided: landing page duplication!');
					break;
				default:
					throw new ErrorException('Duplicated archive provided: reason unknown!');
					break;
			}
		}
	}
}