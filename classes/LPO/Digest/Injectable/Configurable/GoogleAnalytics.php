<?php

namespace LPO\Digest\Injectable\Configurable;

use LPO\Bootstrap;
use LPO\Digest\Injectable\Configurable;
use LPO\Digest\Injectable\JavascriptInjector;
use LPO\Persistence\InjectableType;

class GoogleAnalytics extends Configurable implements JavascriptInjector {

	const VARIABLE_NAME = 'GoogleAnalytics';

	/**
	 * @return int
	 */
	function getType()
	{
		return InjectableType::GOOGLE_ANALYTICS;
	}

	/**
	 * @var string
	 */
	protected static $_source_script = '//-------------------------GA-------------------------
var _gaq = _gaq || [];
_gaq.push(["_setAccount", __tokens.ua]);
_gaq.push(["_trackPageview"]);

(function()
{
var ga = document.createElement("script");
ga.type = "text/javascript";
ga.async = true;
ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
var s = document.getElementsByTagName("script")[0];
s.parentNode.insertBefore(ga, s);
})();';

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