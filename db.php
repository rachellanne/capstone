<?php

$host="sql113.infinityfree.com";
$user="if0_42114084";
$pass="Rheonix123";
$db="if0_42114084_CapstoneProjRheonixDB";

$conn=mysqli_connect($host,$user,$pass,$db);

if(!$conn){
    die("Connection Failed");
}

?>