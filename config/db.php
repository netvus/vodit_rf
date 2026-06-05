<?php
$conn = mysqli_connect("localhost","root","","vodit_rf");

if(!$conn){
    die("Ошибка подключения к БД");
}

mysqli_set_charset($conn,"utf8mb4");
?>
