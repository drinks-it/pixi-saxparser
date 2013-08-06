<?php

require __DIR__.'/../vendor/autoload.php';
require "FileWriter.php";

$exampleXml = fopen("food.xml", "r");

$saxParser = new pixi\Xml\Parser\Sax();
$streamParser->dispatcher->addSubscriber(new Example\Subscriber\FileWriter("FoodInfo.txt"));


while(!feof($exampleXml)) {
    $streamParser->parse(fread($exampleXml, 4096));
}
fclose($exampleXml);


$newFp = fopen("FoodInfo.txt", "r");
while(!feof($newFp)) {
    echo fread($newFp, 4096);
}
fclose($newFp);