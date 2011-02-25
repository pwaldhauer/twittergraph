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


require_once('includes.php');

/**
 * Users to ignore
*/
$ignore = array();

/**
 * Start username
*/
$username = 'knuspermagier';

$tweets = TwitterLoader::loadTweets($username);

$user = new Person();
$user->username = $username;
$user->avatar = TwitterLoader::getAvatarUrl($username);

$persons = array();
$list = new EdgeList();

$persons[] = $user;

do_work($tweets, $user, $list);

$old_persons = array();
foreach($persons as $person) {
	$old_persons[] = $person;
}

foreach($old_persons as $i => $person) {
	$tweets = TwitterLoader::loadTweets($person->username);
	do_work($tweets, $person, $list);
	
	echo "DONE $i of ". count($old_persons) ."\n";
}

print_r($persons);
print_r($list);

$data = array(
	'persons' => $persons,
	'edges' => $list
);

file_put_contents('data', serialize($data));

function personsContains($username) {
	global $persons;
	
	foreach($persons as $person) {
		if($person->username == $username) {
			return $person;
		}
	}
	
	return null;
}


function do_work($tweets, $user, $list) {
	global $persons, $ignore;

	foreach($tweets->status as $status) {
		$text = (string)$status->text;

		preg_match_all('#^@([a-z0-9-_]+)#i', $text, $match);
		$count = count($match[0]);
		
		for($i = 0; $i < $count; $i++) {
			$name = strtolower($match[1][$i]);
		
			if(in_array($name, $ignore)) {
				continue;
			}
		
		
			$person = personsContains($name);
			if($person == null) {
				$person = new Person();
				$person->username = $name;
				$person->avatar = TwitterLoader::getAvatarUrl($name);
			
				$persons[] = $person;
			}
			
			
			$edge = $list->findEdge($user, $person);
			if($edge == null) {
				$edge = new Edge();
				$edge->user1 = $user;
				$edge->user2 = $person;
				
				$list->addEdge($edge);
			}
			
			$edge->replies++;
		}
	}
}
