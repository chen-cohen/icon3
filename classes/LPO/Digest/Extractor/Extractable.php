<?php

namespace LPO\Digest\Extractor;

use Util;

abstract class Extractable
{
	/**
	 * @param string $filePath
	 * @param string $destination
	 * @return boolean
	 */
	abstract public function extract($filePath,$destination);

	/**
	 * @param string $destination
	 * @param int $mode
	 */
	protected function _chmodRecursive($destination, $mode = 0777)
	{
		Util::recursiveChmod($destination,$mode);
	}
}
