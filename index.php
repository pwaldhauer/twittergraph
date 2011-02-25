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

$data = unserialize(file_get_contents('data'));

$maxWidth = 20;
$maxWeight = 0;

foreach($data['edges']->getList() as $edge) {
	if($edge->getWeight() > $maxWeight) {
		$maxWeight = $edge->getWeight();
	}
}

$pw = array();

foreach($data['edges']->getList() as $edge) {
	if(!isset($pw[$edge->user1->username])) {
		$pw[$edge->user1->username] = 0;
	}

	if(!isset($pw[$edge->user2->username])) {
		$pw[$edge->user2->username] = 0;
	}

	$pw[$edge->user1->username]++;
	$pw[$edge->user2->username]++;
}

$maxedges = 0;

foreach($pw as $user => $edges) {
	if($edges > $maxedges) {
		$maxedges = $edges;
	}
}

$minX = 32;
$maxX = isset($_GET['x']) ? intval($_GET['x']) : 1900;
	
$minY = 16;
$maxY = isset($_GET['y']) ? intval($_GET['y']) : 890;

$ignore = array();

$eps = array();
$users = array();
foreach($data['persons'] as $i => $person) {

	$wurstfaktor = 256;
	
	$edgePower = $pw[$person->username]/$maxedges;
	$eps[] = $edgePower;
	
	//echo $person->username ." ep: $edgePower<br>";
	
	$posX = abs($maxX * (-1 * $edgePower) + $minX);
	
	$wurstfaktor = $wurstfaktor * (1- $edgePower);
	$w = rand(-1 * $wurstfaktor, $wurstfaktor);
	
	$posX += $w;
	$posX = $maxX - $posX;
	
	$posX = ($posX > $maxX) ? ($maxX) : $posX;
	
	
	$person->x = $posX;
	$posY = rand($minY, $maxY);
	
	$person->y = $posY;
	
	

	$users[$person->username] = $person;
	
	if($edgePower < 0.03) {
		$ignore[] = $person->username;
	}
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="de-DE">

<head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link type="text/css" rel="stylesheet" href="style.css" />
	
	
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>
        
	<style type="text/css">
	html, body {
		height: 100%;
	}
	
	body {
		padding: 0;
		margin: 0;
	}
	
	.avatar {
		opacity: .3;
	}
	</style>
</head>
<body>


<svg  id="canvas" width="100%" height="100%" version="1.1" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: 99%;">


<?php foreach($data['edges']->getList() as $i => $edge): ?>
	<?php if(in_array($edge->user1->username, $ignore) || in_array($edge->user2->username, $ignore)) continue; ?>

	<line class="edge user-<?php echo $edge->user1->username ?> user-<?php echo $edge->user2->username ?>" data-left="<?php echo $edge->user1->username ?>" data-right="<?php echo $edge->user2->username ?>" x1="<?php echo $users[$edge->user1->username]->x ?>" y1="<?php echo $users[$edge->user1->username]->y ?>"
		x2="<?php echo $users[$edge->user2->username]->x ?>" y2="<?php echo $users[$edge->user2->username]->y ?>" style="stroke: rgba(99,99,99, .3); stroke-width: <?php echo ($edge->getWeight()/$maxWeight) * $maxWidth ?>" title="<?php echo $edge->replies ?>"/>
<?php endforeach ?>

<?php foreach($data['persons'] as $i => $person): ?>

	<?php if(in_array($person->username, $ignore)) continue; ?>	
	<rect id="border-<?php echo $person->username ?>" width="32" height="32" style="fill: #fff; stroke-width: 2; stroke: transparent;"  x="<?php echo $person->x - 16 ?>" y="<?php echo $person->y - 16 ?>"/>
	<image id="user-<?php echo $person->username ?>" class="avatar" x="<?php echo $person->x - 16 ?>" y="<?php echo $person->y - 16 ?>" width="32" height="32" xlink:href="<?php echo $person->avatar ?>" title="<?php echo $person->username ?> (<?php echo $pw[$person->username] ?>)"/>

<?php endforeach ?>


</svg>

<script type="text/javascript">
$('.avatar').hover(function() {
	$(this).css('opacity', 1);
		
	$('.' + $(this).attr('id')).css('stroke', '#000').each(function (el) {
		$('#user-' + $(this).attr('data-left')).css('opacity', 1);
		$('#user-' + $(this).attr('data-right')).css('opacity', 1);
	
		$('#border-' + $(this).attr('data-left')).css('stroke', '#333');
		$('#border-' + $(this).attr('data-right')).css('stroke', '#333');
	});
	
	
	
}, function() {
	$(this).css('opacity', '0.3');
	
	$('.' + $(this).attr('id')).css('stroke', 'rgba(99,99,99, .3)').each(function (el) {
		$('#user-' + $(this).attr('data-left')).css('opacity', '0.3');
		$('#user-' + $(this).attr('data-right')).css('opacity', '0.3');
	
		$('#border-' + $(this).attr('data-left')).css('stroke', 'transparent');
		$('#border-' + $(this).attr('data-right')).css('stroke', 'transparent');
	});;
});

$('.edge').hover(function() {
	$(this).css('stroke', '#000');
	
	$('#user-' + $(this).attr('data-left')).css('opacity', 1);
	$('#user-' + $(this).attr('data-right')).css('opacity', 1);

	$('#border-' + $(this).attr('data-left')).css('stroke', '#333');
	$('#border-' + $(this).attr('data-right')).css('stroke', '#333');

}, function() {
	$(this).css('stroke', 'rgba(99,99,99, .3)');
	
	$('#user-' + $(this).attr('data-left')).css('opacity', '0.3');
	$('#user-' + $(this).attr('data-right')).css('opacity', '0.3');
	
	$('#border-' + $(this).attr('data-left')).css('stroke', 'transparent');
	$('#border-' + $(this).attr('data-right')).css('stroke', 'transparent');

});

</script>

</body>
</html>
