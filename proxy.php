<?php
$u=urldecode($_GET['url']);
$json = file_get_contents($u);
$obj1 = json_decode($json, true);
if($obj1['features'][0]['geometry']['type']!=""){
	echo $json;
}else{
   echo 0;
}
?>