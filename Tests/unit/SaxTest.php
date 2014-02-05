<?php

namespace Pixi\Xml\Parser\Tests;

use Pixi\Xml\Parser\Sax;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SaxTest extends \PHPUnit_Framework_TestCase
{
    public $saxParser;

    public function setup()
    {
        $this->saxParser = new Sax();
    }

    public function testAddingSubscriber()
    {
        $map = array(
            "tag.open" => array("onTagOpen", 0),
            "tag.data" => array("onTagData", 0),
            "tag.close" => array("onTagClose", 0)
        );

        $observer = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventSubscriberInterface')
            ->getMock('Observer');

        $observer::staticExpects($this->once())
            ->method('getSubscribedEvents')
            ->will($this->returnValue($map));

        $this->saxParser->dispatcher->addSubscriber($observer);
    }

    public function testActionOnTagOpen()
    {
        $xml = '
        <root>
            <name>Foo</name>
            <lastname>Bar</lastname>
        </root>';

        $map = array(
            "tag.open" => array("onTagOpen", 0)
        );

        $observer = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventSubscriberInterface')
            ->setMethods(array('onTagOpen','getSubscribedEvents'))
            ->getMock();

        // Implemented function from EventSubscriberInterface
        $observer::staticExpects($this->once())
            ->method('getSubscribedEvents')
            ->will($this->returnValue($map));

        $this->saxParser->dispatcher->addSubscriber($observer);

        // Method for action tag.open
        $observer->expects($this->exactly(3))
            ->method('onTagOpen');

        $this->saxParser->parse($xml);
    }
}
