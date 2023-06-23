<?php
error_reporting(0);
$u=urldecode($_GET['url']);
// mode porxy SSL
$arrContextOptions=array(
	"ssl"=>array(
		"verify_peer"=>false,
		"verify_peer_name"=>false,
	),
); 	
$json = file_get_contents($u, false, stream_context_create($arrContextOptions));
//$json = file_get_contents($u);
$obj1 = json_decode($json, true);
if($obj1['features'][0]['geometry']['type']!=""){
	echo $json;
}else{
   echo 0;
}
?>