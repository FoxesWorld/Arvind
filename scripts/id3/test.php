<?php
require ('getid3.php');

$getid3 = new getID3();
$getid3->encoding = 'UTF-8'; //указываем output-кодировку
$getid3->Analyze('../../files/eventSounds/common/voice1.mp3'); //путь до файла
echo $getid3->info['playtime_string']; //посмотрим результаты работы