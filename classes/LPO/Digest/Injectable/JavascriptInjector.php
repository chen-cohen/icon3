<?php

namespace LPO\Digest\Injectable;

use LPO\Bootstrap;

interface JavascriptInjector {

	/**
	 * @return string
	 */
	function getInjectedScript();

	/**
	 * @return string
	 */
	public static function getVariableName();

	/**
	 * @param Bootstrap $bootstrap
	 *
	 * @return mixed
	 */
	public function getTemplateVariableData(Bootstrap $bootstrap);
}