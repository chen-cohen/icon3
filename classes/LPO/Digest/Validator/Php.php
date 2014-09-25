<?php

namespace LPO\Digest\Validator;

use ErrorException;

class Php {

	const ERR_FILE_NOT_FOUND 	= 1;
	const ERR_INJECTED_PHP_CODE = 2;

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

		if(	strpos($content,'<?')!==false ||
			strpos($content,'?>')!==false ||
			strpos($content,'<%')!==false ||
			strpos($content,'%>')!==false
		)
		{
			return static::ERR_INJECTED_PHP_CODE;
		}

		return true;
	}
}