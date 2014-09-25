<?php

namespace LPO\Digest\Injectable\Configurable;

use LPO\Bootstrap;
use LPO\Digest\Injectable\Configurable;
use LPO\Digest\Injectable\JavascriptInjector;
use LPO\Persistence\InjectableType;

class WotBlocker extends Configurable implements JavascriptInjector {

	const VARIABLE_NAME = 'wotBlocker';

	/**
	 * @return int
	 */
	function getType()
	{
		return InjectableType::WOT_BLOCKER;
	}

	/**
	 * @var string
	 */
	protected static $_source_script = 'var mwt = {e: 0, si: 0, rm: function() { mwt.e++; 20 < mwt.e && clearInterval(mwt.si); c = document.getElementsByTagName("div"); for(i = 0; i < c.length; i++) { c[i] && -1 < c[i].id.indexOf("wot") && c[i].parentNode.removeChild(c[i]) } }}; mwt.rm(); mwt.si = setInterval(mwt.rm, 200); window.onfocus = function() { mwt.e = 0; mwt.rm(); mwt.si = setInterval(mwt.rm, 200); }';

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
		return static::$_source_script;
	}
}
