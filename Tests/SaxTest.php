<?php

namespace Pixi\Xml\Parser\Tests;

use Pixi\Xml\Parser\Sax;

class SaxTest extends \PHPUnit_Framework_TestCase
{
    public $saxParser;

    public function setup()
    {
        $this->saxParser = new Sax();
    }

    public function testTagOpen()
    {
        $xml = '
        <root>
            <name>Foo</name>
            <lastname>Bar</lastname>
        </root>';

        $map = array(
            "tag.open" => array("onTagOpen", 0),
            "tag.data" => array("onTagData", 0),
            "tag.close" => array("onTagClose", 0)
        );

        $observer = $this->getMock('TestObserver');

        $this->saxParser->dispatcher->addSubscriber($observer);
        $this->saxParser->parse($xml);
    }
}

class TestObserver implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            "tag.open" => array("onTagOpen", 0),
            "tag.data" => array("onTagData", 0),
            "tag.close" => array("onTagClose", 0)
        );
    }

    public function onTagOpen($argument)
    {
        // Do something
    }

    public function onTagData($argument)
    {
        // Do something
    }

    public function onTagClose($argument)
    {
        // Do something
    }
}