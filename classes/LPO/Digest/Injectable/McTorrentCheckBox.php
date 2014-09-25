<?php

namespace LPO\Digest\Injectable;

use JsonSerializable;
use LPO\Bootstrap;
use LPO\DownloadUrl;
use Util;

class McTorrentCheckBox implements JavascriptInjector {

	const VARIABLE_NAME = 'McTorrent';

	const SCRIPT = <<<'JS'
function ischez(){ var chek = document.getElementById("chek"); var down =document.getElementsByClassName("dlink"); for (var i = 0; i < down.length; i++) { var obj = down[i]; obj.style.visibility = (chek.checked) ? "" : "hidden"; } _gaq.push(["_trackPageview", '/checkbox']); }
JS;


	/**
	 * @return string
	 */
	function getInjectedScript()
	{
		return '<?php echo $'.self::VARIABLE_NAME.' ?>;';
	}

	/**
	 * @return string
	 */
	public static function getVariableName()
	{
		return self::VARIABLE_NAME;
	}

	/**
	 * @param Bootstrap $bootstrap
	 *
	 * @return string
	 */
	public function getTemplateVariableData(Bootstrap $bootstrap)
	{
		return static::SCRIPT;
	}
}