<?php

// Including the autoloader and an Subscriber
require __DIR__.'/../vendor/autoload.php';
require "FileWriter.php";

$exampleXml = fopen("food.xml", "r");

// Inizializing the parser and adding the included subscriber
$saxParser = new pixi\Xml\Parser\Sax();
$saxParser->dispatcher->addSubscriber(new Example\Subscriber\FileWriter("FoodInfo.txt"));

// Parsing the xml by lines
while(!feof($exampleXml)) {
    $saxParser->parse(fread($exampleXml, 4096));
}
fclose($exampleXml);

// Reading the results of the FileWriter subscriber
$newFp = fopen("FoodInfo.txt", "r");
while(!feof($newFp)) {
    echo fread($newFp, 4096);
}
fclose($newFp);