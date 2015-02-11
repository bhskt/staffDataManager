<?php
function fetch_all($res,$assoc=false){
	$ret=[];
	if($assoc){
		while($row=$res->fetch_assoc()){
			array_push($ret,$row);
		}
	} else {
		while($row=$res->fetch_row()){
			array_push($ret,$row);
		}
	}
	return $ret;
}
function render($page,$ajax){
	ob_clean();
	ob_start();
	$page["loggedIn"]=$_SESSION["loggedIn"];
	$page["title"]=ucwords($page["name"]);
	if(!$ajax){
		require "require/htm/head.htm";
	}
	require "require/htm/header.htm";
	require "require/htm/{$page["name"]}.htm";
	if(!$ajax){
		require "require/htm/foot.htm";
	}
	die();
}
require_once "setup.php";
$page=[];
if(DEVELOPER_MODE){
	error_reporting(E_ALL);
	ini_set("display_errors",true);
	$page["less"]=true;
} else {
$page["less"]=false;
}
session_start();
session_regenerate_id();
if(empty($_SESSION["loggedIn"])){
	$_SESSION["loggedIn"]=false;
}
$page["db"]=new mysqli(DB_HOST,DB_USER,DB_PASSWORD);
if($page["db"]->connect_errno){
	die("MySQLi Connection Error");
}
if(!$page["db"]->select_db(DB_NAME)){
	$page["db"]->query("CREATE DATABASE ".DB_NAME);
	$page["db"]->select_db(DB_NAME);
}
$page["db"]->query("CREATE TABLE IF NOT EXISTS sdm_staff(serial_number INT AUTO_INCREMENT PRIMARY KEY NOT NULL,name TEXT NOT NULL,designation TEXT NOT NULL,address TEXT,date_of_birth DATE NOT NULL,educational_qualification TEXT NOT NULL,date_of_appointment DATE NOT NULL,date_of_confirmation_in_service DATE NOT NULL,date_of_appointment_and_joining_in_present_post DATE NOT NULL,date_of_joining_in_present_office DATE NOT NULL,name_of_previous_office TEXT,date_of_joining_in_previous_office DATE,served_in_north_bengal BOOL,district_or_subdivision TEXT NOT NULL)");
$page["db"]->query("CREATE TABLE IF NOT EXISTS sdm_login(user INT NOT NULL PRIMARY KEY,password TEXT NOT NULL)");
$page["db"]->query("INSERT IGNORE INTO sdm_login VALUES(0,md5('0000'))");
$page["loginPassword"]=$page["db"]->query("SELECT password FROM sdm_login WHERE user=0")->fetch_row()[0];
$page["name"]=$_SESSION["loggedIn"]?"loggedIn":"welcome";
$ajax=(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=="xmlhttprequest");
if($ajax && !empty($_POST)){
	foreach($_POST as $key=>$value){
		$page[$key]=$value;
	}
}
render($page,$ajax);
?>

