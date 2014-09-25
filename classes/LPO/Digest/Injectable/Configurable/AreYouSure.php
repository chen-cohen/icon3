<?php

namespace LPO\Digest\Injectable\Configurable;

use LPO\Bootstrap;
use LPO\Digest\Injectable\Configurable;
use LPO\Digest\Injectable\JavascriptInjector;
use LPO\Persistence\InjectableType;

class AreYouSure extends Configurable implements JavascriptInjector {

	const VARIABLE_NAME = 'areYouSure';

	/**
	 * @return int
	 */
	function getType()
	{
		return InjectableType::ARE_YOU_SURE;
	}

	/**
	 * @var string
	 */
	public static $source_script;

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
	 * @return mixed
	 */
	public function getTemplateVariableData(Bootstrap $bootstrap)
	{
		return static::$source_script;
	}
}

AreYouSure::$source_script = 'window.onbeforeunload = function (e) { return \''.Bootstrap::MESSAGE_FILENAME_DEFAULT_VALUE.'\'; }; var dlinks = document.querySelectorAll(\'a\'); for(var i = 0; i<dlinks.length; i++){ dlinks[i].onclick = function(){ window.onbeforeunload = null; } }';