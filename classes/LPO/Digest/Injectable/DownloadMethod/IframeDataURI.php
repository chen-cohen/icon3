<?php

namespace LPO\Digest\Injectable\DownloadMethod;

use LPO\Bootstrap;
use LPO\Digest\Injectable\DownloadMethod;
use LPO\Digest\Injectable\JavascriptInvokable;

class IframeDataURI extends DownloadMethod implements JavascriptInvokable {

	/**
	 * @var string
	 */
	private static $_source_script = <<<'JS'
window.iframer=function(b,g,d,e){var c=null;"img"!=b.nodeName.toLowerCase()||b.complete?c=h(b,g,d,e):b.onload=function(){c=h(b,g,d,e)};return c}; function h(b,g,d,e){function c(a,c){for(var f=a,d=0,e=0;f&&!isNaN(f.offsetLeft)&&!isNaN(f.offsetTop);)d+=f.offsetLeft,e+=f.offsetTop,f=f.offsetParent;c.style.left=d+"px";c.style.top=e-(g*b.height-b.height)/2+"px"}var k=b.naturalWidth,l=b.naturalHeight,a=document.createElement("iframe");c(b,a);a.style.allowTransparency="true";a.a="none";a.setAttribute("frameBorder","0");a.setAttribute("scrolling","no");a.style.position="absolute";a.style.cursor="pointer";window.chrome?(a.src=e,a.height=l*g,a.width= k,window.addEventListener("resize",function(){c(b,a)},!0)):(a.src=d,a.height=0,a.width=0);document.body.appendChild(a);return a};
JS;
	/**
	 * @var string
	 */
	private static $_invoking_script = <<<'JS'
if(!document.querySelectorAll) { document.querySelectorAll = function(selector) { var doc = document, head = doc.documentElement.firstChild, styleTag = doc.createElement("STYLE"); head.appendChild(styleTag); doc.__qsaels = []; styleTag.styleSheet.cssText = selector + "{x:expression(document.__qsaels.push(this))}"; window.scrollBy(0, 0); return doc.__qsaels; } } function dl() { iframer(document.querySelectorAll(".dlink img")[0], 4, __tokens.downloadLink, __tokens.iframeLink); iframer(document.querySelectorAll(".dlink")[0], 0, __tokens.downloadLink + "&graceful=1", __tokens.downloadLink + "&graceful=1"); } if(__tokens.autoDownload) { dl(); } var links = document.querySelectorAll(".dlink"); for(var i = 0; i < links.length; i++) { var dlink = links[i]; dlink.href = "javascript:void(0)"; dlink.onclick = function(e) { e = e || window.event; if(!e.preventDefault) { e.returnValue = false; } else { e.preventDefault(); } dl(); } }
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