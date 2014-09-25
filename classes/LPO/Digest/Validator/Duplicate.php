<?php

namespace LPO\Digest\Validator;

use GlobIterator;
use LPO\Digest\Digester;

class Duplicate {

	const ERR_FOUND_LP_NAME_DUPLICATE   = 1;

	/**
	 * @param string $landingPageName
	 *
	 * @return bool|int
	 */
	public static function validate($landingPageName)
	{
		$pattern  = BASE.Digester::STATIC_LP_DIR.'*'.DS.Digester::ARCHIVE_FILENAME_REFERENCE;
		$iterator = new GlobIterator($pattern,GlobIterator::CURRENT_AS_PATHNAME);
		foreach($iterator as $file)
		{
			if($landingPageName == trim(file_get_contents($file)))
			{
				return static::ERR_FOUND_LP_NAME_DUPLICATE;
			}
		}
		return true;
	}
}