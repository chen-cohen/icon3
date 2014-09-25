<?php

namespace LPO\Digest\Injectable;

abstract class Configurable {

	/**
	 * @return int
	 */
	abstract function getType();

	/**
	 * @param int $injectableSettings
	 * @return int
	 */
	public function isEnabled($injectableSettings)
	{
		return $injectableSettings & $this->getType();
	}
} 