<?php require_once '../ThumbLib.inc.php';

$thumb = PhpThumbFactory::create('test.jpg');
$thumb->resize(100, 100);
$thumb->show();
?>