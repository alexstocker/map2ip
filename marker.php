<?

$jsonfile = 'markers.json';
$string = file_get_contents($jsonfile);
$json_a = json_decode($string, true);

$id = $_GET['id'];

$out = '';
foreach($json_a['markers'] as $k => $marker){	
	if($marker['id'] == $id){
		foreach($marker as $v){
			$out .= $v.'<br>';
		}
		echo $out;
	}
}

