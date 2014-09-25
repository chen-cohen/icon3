<?php

namespace LPO\Persistence;

class InjectableType {

	const NONE              = 0;
	const AUTO_DOWNLOAD     = 0b1; // 1
	const WOT_BLOCKER       = 0b10; // 2
	const AFTER_CLICK       = 0b100; // 4
	const ARE_YOU_SURE      = 0b1000; // 8
	const GOOGLE_ANALYTICS  = 0b10000; // 16

	const _DEFAULT      = 0b10011; // 19 => InjectableType::AUTO_DOWNLOAD (BIT_OR) InjectableType::WOT_BLOCKER (BIT_OR) InjectableType::GOOGLE_ANALYTICS

}