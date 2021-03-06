<?php
include_once 'preheader.php';
$file = file('../fail2banIPs.txt');
if(isset($_POST['API']) &&  $_POST['API'] === 'true'){
$geo = new geoCode();
$geo->getLatLng($file);
$geo->jsonMarkers();
}

if($_POST['categories'] === 'true'){
$cat = new geoCode();
$cat->getServices();
}

class geoCode {
	
	var $country = '';
	var $address = '';
	var $lat = '';
	var $lng = '';
	var $ip = '';
	var $adate = '';
	var $service = '';
	var $msg = '';
	var $maxfileage = '120'; // minutes
	var $jsonItems = array();
	
	var $jsonfile = "markers.json";

	function findIp($line){
		preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $line, $matches);
		preg_match("/\[.*?\]/",$line,$matches0);
		preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})/", $line, $date);
		$this->adate = $date[0];
		$findIP = $matches[0];
		$this->ip = $findIP;
		$this->service = $matches0[0]; 
		return $findIP;
	}

	function whoIS($ip){
		$city = '';
		$this->address = shell_exec("/usr/bin/whois $ip | grep -oP '(?<=address:).*?(?=address:|$)'");
		$this->country = shell_exec("/usr/bin/whois $ip | grep -im 1 'city\|country'");
		if(empty($this->address) || strlen(trim($this->address)) < 24){
			$this->address = shell_exec("/usr/bin/whois $ip | grep -oP '(?<=Address).*?(?=Zip|$)'");
		}
		$array = array(',',';','c/o');
		$this->address = ltrim($this->address);
		$this->address = rtrim($this->address);
		$this->address = str_ireplace($array,'',$this->address);
		$this->address = preg_replace('/\s+/', '+', $this->address);
		$this->country = ltrim($this->country);
		$this->country = rtrim($this->country);
		$this->country = str_ireplace($array,'',$this->country);
		$this->country = preg_replace('/\s+/', '+', $this->country);
		return true;
	}

	function whereIS($loc) {
		$url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$loc;
		//var_dump($url);
		return $url;
	}

	function curlURL($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response);
	}

	function outPut($response_a) {
		//if ($response_a->results[0]->geometry->location->lat != ''){
		//var_dump($response_a->status);
		if ($response_a->status == 'OK'){
			$lat = $response_a->results[0]->geometry->location->lat;
			$lng = $response_a->results[0]->geometry->location->lng;
		}else{
			$lat = '0.0';
			$lng = '0.0';
		}
		$this->lat = $lat;
		$this->lng = $lng;
		return $lat . '|' . $lng;
	}

	function write($jfile,$output){
		$f = fopen($jfile, "w") or die("Error opening output file");
		file_put_contents($jfile, "");
		fwrite($f, $output); 			
		fclose($f);
	}
	
	function fileage($file){
		$age = (time() - (filemtime($file)))/60;

		if($age < $this->maxfileage){
			return false;
		}else{
			return true;
		}
	}

	function progressBar($percent,$i,$lines) {
/*	
		// Javascript for updating the progress bar and information
		$count = count($lines);
			echo '<script language="javascript">';
			echo 'document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>';
			echo 'document.getElementById("information").innerHTML="Processing '.$i.' from '.$count.' IP\'s.';
			echo '</script>';
			flush();
		if(!empty($this->msg))
		{
		}else{ echo '<li class="listitem" data-value="'.$this->id.'">'.$this->ip.' Lat:'.$this->lat.' Lng:'.$this->lng.' '.str_replace('+',' ',$this->country).'</li>'; 
		echo $this->ip.' Lat: <tab indent=2em>' . $this->lat . ' Long: ' . $this->lng . ' '.str_replace('+',' ',$this->country).'<br>';
		echo str_repeat(' ',1024*64);
		if ($percent = '100'){
		echo '<script language="javascript">document.getElementById("information").innerHTML="Process completed"</script>';
		}
		}
*/
	}
	
	function jsonMarkers(){
//$results = q("SELECT ip.ipID, ip.ipv4, logs.sID, logs.lat, logs.lng, logs.added, services.name FROM ip LEFT JOIN logs ON ip.ipID = logs.ipID LEFT JOIN services ON services.sID = logs.sID");
//$results = q("SELECT ip.ipID, ip.ipv4, services.name FROM ip LEFT JOIN services ON services.sID = ip.sid");
//$results = q("SELECT ip.ipID, ip.ipv4, logs.sID, logs.lat, logs.lng, logs.added, services.name FROM ip LEFT JOIN logs ON ip.ipID = logs.ipID LEFT JOIN services ON logs.sID = services.sID");

$results = q("SELECT logs.logID, ip.ipID, ip.ipv4, logs.sID, logs.lat, logs.lng, logs.added, services.name FROM ip LEFT JOIN logs ON ip.ipID = logs.ipID LEFT JOIN services ON logs.sID = services.sID");

$array = array();
foreach($results as $k => $v){
//var_dump($v);
$array['markers'][] = array('id' => $v['logID'], 'ip' => $v['ipv4'], 'lat' => addslashes($v['lat']), 'long' => addslashes($v['lng']), 'service' => $v['name'], 'created' => $v['added']);
}
//var_dump($array);
//print json_encode($array);		
		/*
		$results = q("SELECT * FROM logs");
		header("Content-type: text/xml");

		$node = $dom->createElement("marker");
		  
		foreach($results as $row){
			$newnode = $parnode->appendChild($node);
			$newnode->setAttribute("name",$row['country']);
		  	$newnode->setAttribute("address", '');
		  	$newnode->setAttribute("lat", $row['lat']);
		  	$newnode->setAttribute("lng", $row['lng']);
		  	$newnode->setAttribute("type", '');
		}

		$dom->saveXML();
		*/
		
print json_encode($array);
return false;
	}
	
	function getServices(){
		$results = q("SELECT sID,name FROM services");
		$list .= "<ul>";
		//$list .= "<li><input type='checkbox' id='allservices'>Alle Dienste</li>";
		foreach($results as $row){
		$c = qr("SELECT COUNT(sID) as count FROM logs WHERE sID = '".$row['sID']."'");
		$rpl = array('[',']');
		$row['name'] = str_replace($rpl,"",$row['name']);
			$list .= '<li><input type="checkbox" id="'.$row['name'].'" onclick=boxclick(this,"'.$row['name'].'")>'.$row['name'].' ('.$c['count'].')</li>';
		}
		$list .= '</ul>';
		echo $list;
	}
	
	public function getLatLng($lines) {
		// Check file age
		//var_dump($this->fileage($this->jsonfile));
		if($this->fileage($this->jsonfile) === true){
//var_dump($this->fileage($this->jsonfile));
		$date = date('Y-m-d H:i:s');
		$count = count($lines);
		$ar = array('[',']','\\','+','/','#','è','é');
		$i = '1';
		$jsontext = "{\"markers\":[";
		$jsonitems = array();

		foreach ($lines as $line_num => $line) {
			if ($i<=$count){
				$percent = intval($i/$count * 100)."%";
				$ip = $this->findIp($line);
				$loc = $this->whoIS($ip);
				if($loc !== false){
					if(!empty($this->address)){
						$url = $this->whereIS($this->address);
						$details = str_replace($ar,' ',$this->address);
					}else{
						$url = $this->whereIS($this->country);
						$details = str_replace($ar,' ',$this->country);
					}
					$service = str_replace($ar,'',$this->service);
					$response_a = $this->curlURL($url);
					if($response_a->status == 'ZERO_RESULTS'){
						$url = $this->whereIS($this->country);
						$response_a = $this->curlURL($url);
					}
					$this->outPut($response_a);
					$this->id = uniqid();
					$jsonitems[$i] = "{\"id\": \"".$this->id."\", \"lat\":  \"".addslashes($this->lat)."\", \"long\":  \"".addslashes($this->lng)."\",\"ip\":\"".$ip."\",\"service\":\"".addslashes($service)."\", \"created\":\"".addslashes($this->adate)."\"},";
					$jsontext .= $jsonitems[$i];
					$this->jsonItems[$i];
				
				}
				
				// Database insert
				// 1. check if ip exists
				$ipexists = q1("SELECT IF( EXISTS(SELECT ipID FROM ip WHERE ipv4 = '$ip' OR ipv6 = '$ip'), 1, 0)");
				if(!$ipexists){
					if(strlen($ip) <= 15) $ipfield = 'ipv4';
					else $ipfield = 'ipv6';
					// Check if Select service
					$serviceexists = q1("SELECT IF( EXISTS(SELECT sID FROM services WHERE name = '".$this->service."' ), 1, 0)");
					if(!$serviceexists) {
						$insertservice = qr("INSERT INTO services SET name = '".$this->service."', added = NOW()");
						$sid = mysql_insert_id();
					}else{
						$sid = qr("SELECT sID FROM services WHERE name = '".$this->service."'");
						$sid = $sid['sID'];
					}
					$ipinsert = qr("INSERT INTO ip SET $ipfield = '$ip', sid = '$sid', added = NOW()");
					$ipID = mysql_insert_id();
				}else{
					$serviceexists = q1("SELECT IF( EXISTS(SELECT sID FROM services WHERE name = '".$this->service."' ), 1, 0)");
					if(!$serviceexists) {
						$insertservice = qr("INSERT INTO services SET name = '".$this->service."', added = NOW()");
						$sid = mysql_insert_id();
					}else{
						$sid = qr("SELECT sID FROM services WHERE name = '".$this->service."'");
						$sid = $sid['sID'];
					}
					$ipID = qr("SELECT ipID FROM ip WHERE ipv4 = '$ip' OR ipv6 = '$ip'");
					$ipID = $ipID['ipID'];
				}
				
				$insertlog = qr("INSERT INTO logs SET ipID = '$ipID', sID = '$sid'," .
						"address0 = '".mysql_real_escape_string($this->address)."'," .
						"address1 = ''," .
						"address2 = ''," .
						"city = ''," .
						"country = '".$this->country."'," .
						"lat = '".$this->lat."'," .
						"lng = '".$this->lng."'," .
						"added = NOW()");
						
				//var_dump($insertlog);
				$i++;
				$this->progressBar($percent,$i,$lines);
				
			} // endif
		} // End foreach
		//ob_end_flush();
		$jsontext = substr_replace($jsontext, '', -1); // to get rid of extra comma
		$jsontext .= "]}";
		$output = $jsontext;
		if($count != 0)	$this->write($this->jsonfile,$output);
		}else{
		$this->msg = 'Please klick on list item';
		$this->progressBar(100,0,$lines);
			$string = file_get_contents($this->jsonfile);
			$json_a = json_decode($string,true);
			$list = '<ul class="iplist">';
			foreach($json_a['markers'] as $marker){
				$list .= '<li data-value="'.$marker['id'].'">'.$marker['ip'].' Lat:'.$marker['lat'].' Lng:'.$marker['long'].'</li>';
			}
			$list .= '</ul>';
			//echo $list;
		}
	} // end getLatLng
} // End geoCode 
?>
