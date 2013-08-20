<?php

class SaxTest extends PHPUnit_Framework_TestCase
{
    public $saxParser;

    public function setup()
    {
        $this->saxParser = new pixi\Xml\Parser\Sax();
    }

    public function testConstruct()
    {
        //var_dump($this->saxParser->dispatcher);exit;
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventDispatcher', $this->saxParser->dispatcher);
    }
}