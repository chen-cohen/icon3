<?php

namespace LPO\Digest\Injectable\DownloadMethod;

use LPO\Bootstrap;
use LPO\Digest\Injectable\DownloadMethod;
use LPO\Digest\Injectable\JavascriptInvokable;

class Direct extends DownloadMethod implements JavascriptInvokable {

	/**
	 * @var string
	 */
	private static $_source_script = <<<'JS'
JS;
	/**
	 * @var string
	 */
	private static $_invoking_script = <<<'JS'
 var links = document.querySelectorAll(".dlink"); for (var i = 0; i < links.length; i++) { var dlink = links[i]; dlink.href = __tokens.downloadLink; dlink.onclick = function () { window._onbeforeunload = window.onbeforeunload; window.onbeforeunload = null; setTimeout(function(){window.onbeforeunload = window._onbeforeunload;},100); } } var cb = function () { if (__tokens.autoDownload) { document.querySelectorAll(".dlink")[0].click() } }; if (window.addEventListener) { window.addEventListener('load', cb, false); } else if (window.attachEvent) { window.attachEvent('onload', cb); }
JS;


	/**
	 * @param Bootstrap $bootstrap
	 *
	 * @return string
	 */
	function getInvokingScript(Bootstrap $bootstrap) { return static::$_invoking_script; }

	/**
	 * @return string
	 */
	public function getSourceScript(){ return static::$_source_script; }
}