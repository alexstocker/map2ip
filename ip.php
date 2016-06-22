<?php require_once('includes/geocode.class.php'); ?>
<!DOCTYPE html>
<html>
<head>
<title>mapIP</title>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<link rel="stylesheet" type="text/css" href="css/style.css">

<!-- Include jQuery lib for AJAX requests --> 
 <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<!-- Include google map lib --> 
<script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyBNvxH37pg_ivNSpanbOYsZD2rgF3iXRX4"></script> 

<script type="text/javascript" src="js/maps2.js"></script>  
</head>
<body onload="loadMap()">
 <div id="myMap"></div>
<!--
<p>
  <label for="amount">Zeitleiste</label>
  <input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;">
</p>
 
<div id="slider-range"></div>
<p>
  <label for="amount">Price range:</label>
  <input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;">
</p>
 
<div id="slider-range"></div>
-->
</body>
</html>
