<?php

namespace LPO\Digest\Extractor;

use ErrorException;
use ZipArchive;

class Zip extends Extractable {

	/**
	 * @param string $filePath
	 * @param string $destination
	 * @param int $mode
	 * @return bool
	 * @throws ErrorException
	 */
	public function extract($filePath,$destination,$mode = 0777)
	{
		$zipper = new ZipArchive();
		$statusCode = $zipper->open($filePath);

		if(	($statusCode !== true && $statusCode !== ZipArchive::ER_OK)
			|| !$zipper->extractTo($destination)
			|| !$zipper->close()
		)
		{
			if(is_numeric($statusCode))
			{
				throw new ErrorException($this->_translateErrorCode($statusCode));
			}

			try
			{
				$zipper->getStatusString();
			}
			catch (ErrorException $e)
			{
				throw $e;
			}
		}

		$this->_chmodRecursive($destination, $mode);

		return true;
	}

	/**
	 * @param int|string $code
	 * @return string
	 */
	public function _translateErrorCode($code)
	{
		switch( (int) $code )
		{
			case ZipArchive::ER_OK           : return 'N No error';
			case ZipArchive::ER_MULTIDISK    : return 'N Multi-disk zip archives not supported';
			case ZipArchive::ER_RENAME       : return 'S Renaming temporary file failed';
			case ZipArchive::ER_CLOSE        : return 'S Closing zip archive failed';
			case ZipArchive::ER_SEEK         : return 'S Seek error';
			case ZipArchive::ER_READ         : return 'S Read error';
			case ZipArchive::ER_WRITE        : return 'S Write error';
			case ZipArchive::ER_CRC          : return 'N CRC error';
			case ZipArchive::ER_ZIPCLOSED    : return 'N Containing zip archive was closed';
			case ZipArchive::ER_NOENT        : return 'N No such file';
			case ZipArchive::ER_EXISTS       : return 'N File already exists';
			case ZipArchive::ER_OPEN         : return 'S Can\'t open file';
			case ZipArchive::ER_TMPOPEN      : return 'S Failure to create temporary file';
			case ZipArchive::ER_ZLIB         : return 'Z Zlib error';
			case ZipArchive::ER_MEMORY       : return 'N Malloc failure';
			case ZipArchive::ER_CHANGED      : return 'N Entry has been changed';
			case ZipArchive::ER_COMPNOTSUPP  : return 'N Compression method not supported';
			case ZipArchive::ER_EOF          : return 'N Premature EOF';
			case ZipArchive::ER_INVAL        : return 'N Invalid argument';
			case ZipArchive::ER_NOZIP        : return 'N Not a zip archive';
			case ZipArchive::ER_INTERNAL     : return 'N Internal error';
			case ZipArchive::ER_INCONS       : return 'N Zip archive inconsistent';
			case ZipArchive::ER_REMOVE       : return 'S Can\'t remove file';
			case ZipArchive::ER_DELETED      : return 'N Entry has been deleted';

			default: return sprintf('Unknown error code %s', $code );
		}
	}
}