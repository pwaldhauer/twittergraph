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

class EdgeList {

	private $edges;
	
	public function __construct() {
		$this->edges = array();
	}
	
	public function addEdge(Edge $edge) {
		$this->edges[] = $edge;
	}
	
	public function findEdge(Person $p1, Person $p2) {
		foreach($this->edges as $edge) {
		 
			if(($edge->user1 == $p1 && $edge->user2 == $p2) || ($edge->user1 == $p2 && $edge->user2 == $p1)) {
				return $edge;
			}
		
		}
		
		return null;
	}
	
	public function getList() {
		return $this->edges;
	}
}