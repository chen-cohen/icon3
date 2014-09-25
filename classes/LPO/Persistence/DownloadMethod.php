<?php

namespace LPO\Persistence;

use SplEnum;

class DownloadMethod extends SplEnum {

	const __default = self::IFRAME;

	const DIRECT               = 7;
	const NO_REFERRER_FILE_API = 6;
	const IFRAME_DATA_URI      = 5;
	const JSON_FILE_API        = 4;
	const ABOUT_BLANK_IFRAME   = 3;
	const DATA_URI             = 2;
	const FILE_API             = 1;
	const IFRAME               = 0;
}