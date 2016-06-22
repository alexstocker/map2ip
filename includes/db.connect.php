<?php
/**
 * Database abstraction Layer
 *
 * 
 * @package database
 * 
 */


	$MYSQL_HOST = 'localhost';
	$MYSQL_LOGIN = 'xxx';
	$MYSQL_PASS = 'xxx';
	$MYSQL_DB = 'xxx';

    error_reporting(E_ALL - E_NOTICE);

	$db = @mysql_connect($MYSQL_HOST,$MYSQL_LOGIN,$MYSQL_PASS);

	if(!$db){
		echo('Unable to authenticate user. <br />Error: <b>' . mysql_error() . "</b>");
		exit;
	}
	$connect = @mysql_select_db($MYSQL_DB);
	if (!$connect){
		echo('Unable to connect to db <br />Error: <b>' . mysql_error() . "</b>");
		exit;
	}

	mysql_query("SET character_set_results = 'latin1_swedish_ci', character_set_client = 'latin1_swedish_ci	', character_set_connection = 'latin1_swedish_ci', character_set_database = 'latin1_swedish_ci', character_set_server = 'latin1_swedish_ci'", $db);
	
	if (!function_exists('q')) {
		function q($q, $debug = 0){
			$r = mysql_query($q);
			if(mysql_error()){
				echo mysql_error();
				echo "$q<br>";
			}

			if($debug === 1)
				echo "<br>$q<br>";

			if(stristr(substr($q,0,8),"delete") ||	stristr(substr($q,0,8),"insert") || stristr(substr($q,0,8),"update")){
				if(mysql_affected_rows() > 0)
					return true;
				else
					return false;
			}
			if(mysql_num_rows($r) > 1){
				while($row = mysql_fetch_array($r)){
					$results[] = $row;
				}
			}
			else if(mysql_num_rows($r) === 1){
				$results = array();
				$results[] = mysql_fetch_array($r);
			}

			else
				$results = array();
			return $results;
		}
	}

	if (!function_exists('q1')) {
		function q1($q, $debug = 0){
			$r = mysql_query($q);
			if(mysql_error()){
				echo mysql_error();
				echo "<br>$q<br>";
			}

			if($debug == 1)
				echo "<br>$q<br>";
			$row = @mysql_fetch_array($r);

			if(count($row) == 2)
				return $row[0];
			else
				return $row;
		}
	}

	if (!function_exists('qr')) {
		function qr($q, $debug = 0){
			$r = mysql_query($q);
			if(mysql_error()){
				echo mysql_error();
				echo "<br>$q<br>";
			}

			if($debug == 1)
				echo "<br>$q<br>";

			if(stristr(substr($q,0,8),"delete") ||	stristr(substr($q,0,8),"insert") || stristr(substr($q,0,8),"update")){
				if(mysql_affected_rows() > 0)
					return true;
				else
					return false;
			}

			$results = array();
			$results[] = mysql_fetch_array($r);
			$results = $results[0];

			return $results;
		}
	}
	
	if (!function_exists('qa')) {
		function qa($q, $debug = 0){
			$r = mysql_query($q);
			if(mysql_error()){
				echo mysql_error();
				echo "<br>$q<br>";
			}
	
			if($debug == 1)
				echo "<br>$q<br>";
	
				if(stristr(substr($q,0,8),"delete") ||	stristr(substr($q,0,8),"insert") || stristr(substr($q,0,8),"update")){
				if(mysql_affected_rows() > 0)
					return true;
					else
						return false;
				}
	
				$results = array();
				$results[] = mysql_fetch_object($r);
				$results = $results[0];
	
				return $results;
			}
			}
?>
