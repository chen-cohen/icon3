<?php

namespace LPO\Digest\Injectable;

use LPO\Bootstrap;

interface JavascriptInvokable {

	/**
	 * @param Bootstrap $bootstrap
	 *
	 * @return mixed
	 */
	function getInvokingScript(Bootstrap $bootstrap);

	/**
	 * @return string
	 */
	function getSourceScript();
}