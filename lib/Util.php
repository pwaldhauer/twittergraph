<?php
/******************************************************************************
*
* twittergraph - Just some twitter graphing tests
* Copyright (C) 2011 Philipp Waldhauer
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful, but
* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
* or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
* more details.
*
* You should have received a copy of the GNU General Public License along with
* this program; if not, write to the Free Software Foundation, Inc.,
* 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*
* *************************************************************************** */

class TwitterLoader {


	public static function loadTweets($username) {
		$url = 'http://twitter.com/statuses/user_timeline/'. $username .'.xml?count=200';
		
		echo "requesting timeline of $username\n";
		
		$xml = self::getFromCache($url);
		return $xml;
	}

	public static function getAvatarUrl($username) {
		$url = 'http://api.twitter.com/1/users/show.xml?screen_name='. $username;
		
		echo "requesting $username ...\n";
		$xml = self::getFromCache($url);
				
		return (string)$xml->profile_image_url;
	}	
	
	public static function getFromCache($url) {
		$hash = 'cache/'. md5(strtolower($url));
		
		if(file_exists($hash)) {
			return simplexml_load_file($hash);
		} else {
			$xml = simplexml_load_file($url);
			file_put_contents($hash, $xml->asXML());
			return $xml;
		}
		
	}

}

