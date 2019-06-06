<?php
require_once "./CreateAndFill.php";
require_once "./Performance.php";

//создание таблиц и заполнение их данными
$CreateAndFill = new CreateAndFill();
$CreateAndFill->run();

//
$Performance = new Performance();
$Performance->run();

?>
