<?php
/* 
	-Easy Controll Ultimate(ECTRu)-
	Author: Just Adil
	GitHub: https://GitHub.com/A01L
	Version: ECTR 0.6v ULTIMATE
 */


//Set all config for gl-obal
require_once "config.php";
require_once 'Mobile_Detect.php';
include('qrlib.php');
//For function QrGen
$qr = new QR_BarCode();
session_start();

//Logging function
function event($log, $cat = 'none'){
	mysqli_query($GLOBALS['db'], "INSERT INTO `log`(`cat`, `event`) VALUES ('$cat','$log')");
}


//Randomeizer number
function rand_n($length = 6)
	{
		$arr = array(
			'1', '2', '3', '4', '5', '6', '7', '8', '9', '0'
		);
	 
		$res = '';
		for ($i = 0; $i < $length; $i++) {
			$res .= $arr[random_int(0, count($arr) - 1)];
		}
		return $res;
	}

//Get and saver file
	function get_file($path, $name, $newn = "null"){
		if (!@copy($_FILES["$name"]['tmp_name'], $path.$_FILES["$name"]['name'])){
			return 1;
			}
		else {
			$fn = $_FILES["$name"]['name'];
			$type = format($fn);
			if ($newn != "null") {
				rename("$path$fn", "$path$newn.$type");
				return "$newn.$type";
			}
			else{
				$fnn=str_replace( " " , "_" , $_FILES["$name"]['name']);
				rename("$path$fn", "$path$fnn");
				return "$fnn";
			}
		}
	}


//Get foramt from file
	function format($file){
		 $temp= explode('.',$file);
		 $extension = end($temp);
		 return $extension;
	}

//Start classes

// data base control
class DBControl {
	public $connect;
	public $table;

	//Count rows
	public function count($ma){
		$table = $this->table;
		$query = "SELECT * FROM `".$table."` WHERE ";
		$value = "VALUES (";
		foreach(array_keys($ma) as $key){
			$query = $query."`".$key."` = '".$ma["$key"]."' AND ";
		}
		$query = mb_eregi_replace("(.*)[^.]{4}$", '\\1', $query);
		$sql = $query;

		$conn = $this->connect;  
		if($result = $conn->query($sql)){
			$rowsCount = $result->num_rows; // ID - constant
			return $rowsCount;
			//return $rowsCount;
			$result->free();
		}

	}

	//Add data to table
	public function insert($values){
		$table = $this->table;
		$ma = $values;
		$query = "INSERT INTO `".$table."`(";
		$value = "VALUES (";
		foreach(array_keys($ma) as $key){
			$query = $query."`".$key."`, ";
		}
		$query = mb_eregi_replace("(.*)[^.]{2}$", '\\1', $query);
		$query = $query.")";
		foreach(array_keys($ma) as $key){
			$value = $value."'".$ma["$key"]."', ";
		}
		$value = mb_eregi_replace("(.*)[^.]{2}$", '\\1', $value);
		$value = $value.")";
		$full = $query." ".$value;
		mysqli_query($GLOBALS['ectr_connect'], $full);
		return $full;
	}

	//Update data in table
	public function update($data, $id){
		$table = $this->table;
		$query = "UPDATE `".$table."` SET ";
		foreach(array_keys($data) as $key){
			$query = $query."`".$key."` = '".$data["$key"]."', ";
		}
		$query = mb_eregi_replace("(.*)[^.]{2}$", '\\1', $query);
		$query = $query." WHERE `id` = ".$id;
		mysqli_query($GLOBALS['ectr_connect'], $query);
		return $query;
	}

	//Get data from table
	public function select($index, $index2, $data='a'){
			

	if($data != 'a'){
		$db = $this->connect;  
		$table = $this->table;
        $querry = mysqli_query($db, "SELECT * FROM `$table` WHERE `$index` = '$index2'");
        $datas = mysqli_fetch_assoc($querry);
        return $datas["$data"];
	}
	else{
		$db = $this->connect;  
		$table = $this->table;
        $querry = mysqli_query($db, "SELECT * FROM `$table` WHERE `$index` = '$index2'");
        $datas = mysqli_fetch_assoc($querry);
        return $datas;
	}
    }

	//Delete data
	public function delete($index, $index2){
		$db = $this->connect;  
		$table = $this->table;
    	mysqli_query($db, "DELETE FROM `$table` WHERE `$index` = $index2");
    }

}

//Text control
class STControl {
	
	public $text;

	//cuting text
	public static function cut($start, $textf, $end){
    $t1 = mb_eregi_replace("(.*)[^.]{".$end."}$", '\\1', $textf);
    $t2 = mb_eregi_replace("^.{".$start."}(.*)$", '\\1', $t1);
    $textf = $t2;
    return $textf;
	}

	//get format (value after dote)
	public static function format($file){
		 $temp= explode('.',$file);
		 $extension = end($temp);
		 return $extension;
	}

}

//Data control
class DTControl{
	public $name;
	public $path;
	public $link;
	public $data;

	//set ECTR js lib
	Public static function setMJS(){
		echo "<script type='text/javascript' src='".Router::host()."/ectr/main.js'></script>";
	}

	//set local jquery to page
	public static function setjquery(){
		echo "<script type='text/javascript' src='".Router::host()."/ectr/jquery.min.js'></script>";
		// echo '<script type="text/javascript">
		// function sendAjaxForm(result_form, ajax_form, url) {
		// 	$.ajax({
		// 		url:     url, //url страницы 
		// 		type:     "POST", //метод отправки
		// 		dataType: "html", //формат данных
		// 		data: $("#"+ajax_form).serialize(),  // Сеарилизуем объект
		// 		success: function(response) { //Данные отправлены успешно
		// 			result = $.parseJSON(response);
		// 			$("#result_form").html("success");
		// 		},
		// 		error: function(response) { // Данные не отправлены
		// 			$("#result_form").html("failed");
		// 		}
		// 	 });
		// }
		// </script>';
	}

	//set ajax form controller
	public static function ajax($btn, $form, $url){
		echo '<script type="text/javascript">
		$( document ).ready(function() {
			$("#'.$btn.'").click(
				function(){
					sendAjaxForm("rf", "'.$form.'", "'.$url.'");
					return false; 
				}
			);
		});
		</script>
		';
	}

	//Storage control
	public function storage(){
	$path_storage = $this->path;
	$name_form = $this->name;

	date_default_timezone_set('UTC');

	$dir = date("Ym");
	
	$gen1 = date("dHis"); 
	$gen2 = rand_n(3);
	$fname = "$gen1$gen2";

	if (is_dir ($dir)) {
		get_file("$path_storage/$dir/", $name_form, $fname);
	} 
	else {
		mkdir("$path_storage/$dir", 0777, true);
		get_file("$path_storage/$dir/", $name_form, $fname);
	}

	$ex = format($_FILES["$name_form"]['name']);
	$call_back = array(
		"name" => "$fname.$ex",
		"path" => "$dir/"
	);
	return $call_back;
	}

	//Getting file 
	public function save($newn = "null"){
		$name = $this->name;
		$path = $this->path;

		function format($file){
			 $temp= explode('.',$file);
			 $extension = end($temp);
			 return $extension;
		}

		if (!@copy($_FILES["$name"]['tmp_name'], $path.$_FILES["$name"]['name'])){
			return 'error';
			}
		else {
			$fn = $_FILES["$name"]['name'];
			$type = format($fn);
			if ($newn != "null") {
				rename("$path$fn", "$path$newn.$type");
				return "$newn.$type";
			}
			else{
				$fnn=str_replace( " " , "_" , $_FILES["$name"]['name']);
				rename("$path$fn", "$path$fnn");
				return "$fnn";
			}
		}
	}

	//Send [GET] data to link
	public function oget(){
			$data = $this->data;
			$link = $this->link;
			$ch = curl_init("$link?" . http_build_query($data));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$return = curl_exec($ch);
			curl_close($ch);
			return $return;
		
	}

	//Send [POST] data to link
	public function opost(){
			$data = $this->data;
			$link = $this->link;
			$ch = curl_init("$link");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			$re = curl_exec($ch);
			curl_close($ch);	
			return $re;
		
	}

	//Get all data in global param GET, POST
	public function form($type){
		if ($type == "post" OR $type == "p") {
			$a = array();
			if (isset($_POST)){
			    foreach ($_POST as $key=>$value){
			        $a[$key]=$value;
			    }
			}
			print_r($a);
		}
		elseif ($type == "get" OR $type == "g") {
			$a = array();
			if (isset($_GET)){
			    foreach ($_GET as $key=>$value){
			        $a[$key]=$value;
			    }
			}
			print_r($a);
		}
		else{
			echo "Error type!";
		}
	}

}

//Router controller
class Router {
	//Routing for getting view (adding new url)
	public static function get($link, $path){
		global $routes_ectr;
		$routes_ectr["$link"] = "$path";
	}

	//Router VIEW activator 
	public static function on(){
		global $routes_ectr;
		global $routes_ectr_404;

		$url = $_SERVER['REQUEST_URI'];
		$url = explode('?', $url);
		$url = $url[0];
		if (array_key_exists($url, $routes_ectr)) {
			$handler = $routes_ectr[$url];
			include "view/".$handler;
		} else {
			include $routes_ectr_404;
		}
	}

	//Redirecting 
	public static function redirect($url, $sleep = 0){
		header('Refresh: '.$sleep.'; url='.$url);
		exit();
	}

	//Get host (may add path)
	public static function host($path = ""){
		if ($path) {
			$link="$path";
		}
		else {
			$link=null;
		}
			$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$link";		
		
		return $actual_link;
	}
	//Get full url
	public static function url(){
		$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		return $actual_link;
	}

	//Get info about useragent
	public static function agent(){
		$detect = new Mobile_Detect;

		if($detect->isTablet() ) { $device = 'Tablet'; }
		elseif($detect->isMobile() && !$detect->isTablet() ) { $device = 'Phone'; }
		else{ $device = "PC";}

		if($detect->isiOS() ) { $os = 'IOS'; }
		elseif($detect->isAndroidOS() ) { $os = 'AOS'; }
		elseif($detect->isBlackBerryO() ) { $os = 'BB'; }
		elseif($detect->iswebOS() ) { $os = 'WOS'; }
		else{ $os = 'Windows OS';}


		if($detect->isiPhone() ) { $phone = 'iPhone'; }
		elseif($detect->isSamsung() ) { $phone = 'Samsung'; }
		elseif($detect->isBlackBerry() ) { $phone = 'BlackBery'; }
		elseif($detect->isSony() ) { $phone = 'Sony'; }
		else{ $phone = 'null';}


		if($detect->isChrome() ) { $browser = 'Chrome'; }
		if($detect->isOpera() ) { $browser = 'Opera'; }
		if($detect->isSafari() ) { $browser = 'Safari'; }
		if($detect->isEdge() ) { $browser = 'Edge'; }
		if($detect->isIE() ) { $browser = 'IE'; }
		if($detect->isFirefox() ) { $browser = 'Firefox'; }
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE) {
			$browser = 'Chrome';
		}
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'OPR') !== FALSE) {
			$browser = 'Opera';
		}
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'YaBrowser') !== FALSE) {
			$browser = 'Yandex';
		}
		else {
			$browser = 'null';
		}


		$ip = $_SERVER['REMOTE_ADDR']; 
			$query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip.'?lang=ru'));
			if($query && $query['status'] == 'success') {
				$country = $query['country'];
				$city = $query['city'];
				} else {
				$country = "null";
				$city = "null";
			}
		$today = date("Y-m-d H:i"); 
		
		$data = array(
			'time' => $today,
			'country' => $country,
			'region' => $city,
			'device' => $device,
			'phone' => $phone,
			'browser' => $browser,
			'os' => $os,
		);

		return $data;

	}
}

//Session controller
class Session {

	public $name;

	//Create new Session
	public function new($id, $key = 'none'){
		$name = $this->name;

		$_SESSION["$name"] = [
            "id" => $id,
            "key" => $key,
        ];
	}

	//Close Session
	public static function close($name){
		unset($_SESSION["$name"]);
	}

	//Route if not session
	public function not($locate){
		$name = $this->name;
		if (!$_SESSION["$name"]) {
    		header('Location: '.$locate);
		}
	}

	//Route if have Session
	public function yes($locate){
		$name = $this->name;
		if ($_SESSION["$name"]) {
    		header('Location: '.$locate);
		}
	}

	//Check session (have = 1 / not = 0)
	public function check(){
		$name = $this->name;
		if (!$_SESSION["$name"]) {
    		return 0;
		}
		else{
			return 1;
		}
	}


}

//Other tools
class Gen {

	//Qr Generate example: qr('https://jcodes.space', 'my/adil.png')
	public static function qr($data, $path){
		$qr = new QR_BarCode();
        $qr->info($data);
        $qr->qrCode(500, $path);
	}

	//Generate random symbols
	public static function str($length = 6)
	{
		$arr = array(
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 
			'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
		);
	 
		$res = '';
		for ($i = 0; $i < $length; $i++) {
			$res .= $arr[random_int(0, count($arr) - 1)];
		}
		return $res;
	}
	
	//Generate random numbers
	public static function int($length = 6)
	{
		$arr = array(
			'1', '2', '3', '4', '5', '6', '7', '8', '9', '0'
		);
	 
		$res = '';
		for ($i = 0; $i < $length; $i++) {
			$res .= $arr[random_int(0, count($arr) - 1)];
		}
		return $res;
	}

	//Generate random (numbers and symbols)
	public static function mix($length = 6)
	{
		$arr = array(
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 
			'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 
			'1', '2', '3', '4', '5', '6', '7', '8', '9', '0'
		);
	 
		$res = '';
		for ($i = 0; $i < $length; $i++) {
			$res .= $arr[random_int(0, count($arr) - 1)];
		}
		return $res;
	}

	//Generate link for whatsapp
	public static function wame($number, $msg = " "){
		$data=urlencode($msg);
		$link="https://api.whatsapp.com/send?phone=$number&text=$data";
		return $link;
	}
}

//JSON in ECTR
class JSON{
	public $data;
	public $json;

	//json decoder
	public function decode(){
		$json = json_decode($this->json, true);
		$this->data = $json;
		return $json;
	}

	//json encoder
	public function encode($data){
		$json = json_encode($data);
		// $this->json = $json;
		return $json;
	}

	//add data to JSON
	public function set($data){
		$json = json_decode($this->json, true);
		$new = array_merge($json, $data);
		$json = json_encode($new);
		$this->json = $json;
		return $json;
	}
}

//Tracking function
function track($name, $type, $oid='0'){
	$db = new DBControl;
	$db->connect = $GLOBAL['db'];
	$db->table = 'tracker';
	
	$data = Router::agent();

	$table = array(
		'oid' => $oid,
		'type' => $type,
		'name' => $name,
		'url' => Router::url(),
		'time' => $data['time'],
		'country' => $data['country'],
		'region' => $data['region'],
		'device' => $data['device'],
		'browser' => $data['browser'],
		'os' => $data['os']
	);

	$db->insert($table);
}

?>