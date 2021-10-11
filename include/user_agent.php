<?php

/**
 * Copyright (C) 2014-2016 Visman (mio.visman@yandex.ru)
 * Copyright (C) 2012 Daris (daris91@gmail.com)
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */

function ua_get_filename($name, $folder)
{
	$name = preg_replace('%[^\w]%', '', strtolower($name));
	return get_base_url(true).'/img/user_agent/'.$folder.'/'.$name.'.png';
}

function ua_search_for_item($items, $usrag)
{
	foreach ($items as $item)
	{
		if (strpos($usrag, strtolower($item)) !== false)
			return $item;
	}

	return 'Unknown';
}

function get_useragent_names($usrag)
{
	$browser_img = $browser_version = '';
	
	$usrag = strtolower($usrag);
	
	// Browser detection
	$browsers = array('Opera', 'Avant', 'Maxthon', 'Edge', 'MSIE', 'OPR', 'YaBrowser', 'Chromium', 'Vivaldi', 'Chrome', 'Arora', 'GNOME Web', 'Galeon', 'iCab', 'Konqueror', 'Safari', 'Flock', 'Iceweasel', 'SeaMonkey', 'Netscape', 'K-Meleon', 'Firefox', 'Camino', 'Trident');

	$browser = ua_search_for_item($browsers, $usrag);

	if (preg_match('#'.preg_quote(strtolower((in_array($browser, array('Safari', 'Opera')) ? 'Version' : ($browser == 'Trident' ? 'rv:' : $browser)))).'[\s/]*([\.0-9]+)#', $usrag, $matches))
	{
		$matches = explode('.', $matches[1]);
		$browser_version = $matches[0].(isset($matches[1]) ? '.'.$matches[1] : '');
	}

	if ($browser == 'Trident' && !empty($browser_version) || $browser == 'MSIE')
	{
		if (intval($browser_version) >= 9)
			$browser_img = 'Internet Explorer 9';

		elseif (intval($browser_version) >= 7)
			$browser_img = 'Internet Explorer 7';

		$browser = 'Internet Explorer';
	}

	elseif ($browser == 'Edge')
		$browser = 'Microsoft Edge';

	elseif ($browser == 'OPR')
		$browser = 'Opera';

	// System detection
	$systems = array('Windows', 'Linux', 'Mac', 'Android', 'Amiga', 'BeOS', 'FreeBSD', 'HP-UX', 'NetBSD', 'OS/2', 'SunOS', 'Symbian', 'Unix', 'J2ME/MIDP', 'BlackBerry', 'BB10');
	
	$system = ua_search_for_item($systems, $usrag);
	
	if ($system == 'Linux')
	{
		$systems = array('Android', 'CentOS', 'Debian', 'Fedora', 'Freespire', 'Gentoo', 'Katonix', 'KateOS', 'Knoppix', 'Kubuntu', 'Linspire', 'Mandriva', 'Mandrake', 'RedHat', 'Slackware', 'Slax', 'Suse', 'Xubuntu', 'Ubuntu', 'Xandros', 'Arch', 'Ark');

		$system = ua_search_for_item($systems, $usrag);

		if ($system == 'Unknown')
			$system = 'Linux';

		elseif ($system == 'Mandrake')
			$system = 'Mandriva';
	}

	elseif ($system == 'Windows')
	{
		preg_match('#windows nt ([\.0-9]+)#', $usrag, $matches);
		$version = isset($matches[1]) ? $matches[1] : '';

		switch ($version) {
			case '6.1':
				$system = 'Windows 7';
				break;
			case '6.3':
				$system = 'Windows 8.1';
				break;
			case '5.1':
			case '5.2':
				$system = 'Windows XP';
				break;
			case '6.2':
				$system = 'Windows 8';
				break;
			case '6.0':
				$system = 'Windows Vista';
				break;
			case '10.0':
				$system = 'Windows 10';
				break;
			case '11.0':
				$system = 'Windows 11';
				break;
		}
	}

	elseif ($system == 'Mac')
		$system = 'Macintosh';

	elseif ($system == 'BB10')
		$system = 'BlackBerry';

	if (empty($browser_img))
		$browser_img = $browser;

	$result = array(
		'system'					=> $system,
		'browser_img'			=> $browser_img,
		'browser_name'		=> $browser.' '.$browser_version
	);

	return $result;
}

function get_useragent_icons($usrag)
{
	global $pun_user;
	static $uac = array();

	if ($usrag == '') return '';
		
	if (isset($uac[$usrag])) return $uac[$usrag];
		
	$agent = get_useragent_names($usrag);

	$result = '<img src="'.ua_get_filename($agent['system'], 'system').'" title="'.pun_htmlspecialchars($agent['system']).'" alt="'.pun_htmlspecialchars($agent['system']).'" style="margin-right: 1px"/>';
	$result .= '<img src="'.ua_get_filename($agent['browser_img'], 'browser').'" title="'.pun_htmlspecialchars($agent['browser_name']).'" alt="'.pun_htmlspecialchars($agent['browser_name']).'" style="margin-left: 1px"/>';

	$desc = ($pun_user['is_admmod']) ? ' style="cursor: pointer" onclick="alert(\''.pun_htmlspecialchars(addslashes($usrag).'\n\nSystem:\t'.addslashes($agent['system']).'\nBrowser:\t'.addslashes($agent['browser_name'])).'\')"' : '';

	$result = "\t\t\t\t\t\t".'<dd class="usercontacts"><span class="user-agent"'.$desc.'>'.$result.'</span></dd>'."\n";

	$uac[$usrag] = $result;
	return $result;
}
