<!DOCTYPE html>

<html>
<head>
<title>mapIP</title>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<link rel="stylesheet" type="text/css" href="css/style.css">

<!-- Include jQuery lib for AJAX requests --> 
<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script> 
 
<!-- Include google map lib --> 
<script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyBNvxH37pg_ivNSpanbOYsZD2rgF3iXRX4&sensor=false"></script> 

<script type="text/javascript" src="js/maps.js"></script> 
<script type="text/javascript" src="js/dragme.js"></script> 
</head>
<body onload="loadMap()">
	<div id="gitlink">
		<a href="https://gitorious.org/mapip"><img src="blank.gif"
			width="125px" height="130px"> </a>
	</div>
 <div id="myMap"></div>
<div class="markers" id="dragme" draggable="true">
<div id="progress" style="width:99%;border:1px solid #ccc;"></div>
<div id="information" style="width"></div>
<?php
require_once('includes/geocode.class.php');
$geo = new geoCode();
$geo->getLatLng($file);
?>
</div>
<script type="text/javascript" src="js/dragme.js"></script>
</body>
</html>
