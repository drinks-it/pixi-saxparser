<?php

namespace Pixi\Xml\Parser\Tests;

use Pixi\Xml\Parser\Sax;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SaxTest extends \PHPUnit_Framework_TestCase
{
    private $saxParser;

    /* Some test xml structure */
    private $xml = '
        <root>
            <name>Foo</name>
            <lastname>Bar</lastname>
        </root>';

    protected function setUp()
    {
        $this->saxParser = new Sax();
    }

    protected function tearDown()
    {
        $this->saxParser = null;
    }

    public function testAddSubscriber()
    {
        $map = array(
            "tag.open" => array("onTagOpen", 0),
            "tag.data" => array("onTagData", 0),
            "tag.close" => array("onTagClose", 0)
        );

        // Mocking an observer
        $observer = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventSubscriberInterface')
            ->getMock('Observer');

        // Implemented function from EventSubscriberInterface
        $observer::staticExpects($this->once())
            ->method('getSubscribedEvents')
            ->will($this->returnValue($map));

        // Test if adding a subscriber was successfull
        $this->saxParser->dispatcher->addSubscriber($observer);
    }

    public function testEventTagOpen()
    {
        $map = array(
            "tag.open" => array("onTagOpen", 0)
        );

        // Mocking an observer
        $observer = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventSubscriberInterface')
            ->setMethods(array('onTagOpen','getSubscribedEvents'))
            ->getMock();

        // Implemented function from EventSubscriberInterface
        $observer::staticExpects($this->once())
            ->method('getSubscribedEvents')
            ->will($this->returnValue($map));
        $this->saxParser->dispatcher->addSubscriber($observer);

        // Test for tag.open event
        $observer->expects($this->exactly(3))
            ->method('onTagOpen');
        $this->saxParser->parse($this->xml);
    }

    public function testEventTagClose()
    {
        $map = array(
            "tag.close" => array("onTagClose", 0)
        );

        // Mocking an observer
        $observer = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventSubscriberInterface')
            ->setMethods(array('onTagClose','getSubscribedEvents'))
            ->getMock();

        // Implemented function from EventSubscriberInterface
        $observer::staticExpects($this->once())
            ->method('getSubscribedEvents')
            ->will($this->returnValue($map));
        $this->saxParser->dispatcher->addSubscriber($observer);

        // Test for tag.close event
        $observer->expects($this->exactly(3))
            ->method('onTagClose');
        $this->saxParser->parse($this->xml);
    }

    public function testEventTagData()
    {
        $map = array(
            "tag.data" => array("onTagData", 0)
        );

        // Mocking an observer
        $observer = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventSubscriberInterface')
            ->setMethods(array('onTagData','getSubscribedEvents'))
            ->getMock();

        // Implemented function from EventSubscriberInterface
        $observer::staticExpects($this->once())
            ->method('getSubscribedEvents')
            ->will($this->returnValue($map));
        $this->saxParser->dispatcher->addSubscriber($observer);

        /**
         * Test for tag.data event
         *
         * The tag.data event is called once for each node.
         * It's again called if this node has a value and even more times if this value
         * is very long.
         * If the node is nested it gets additionally called for each level.
         *
         * Root is called once, because it doesn't have a value inside
         * Name and Lastname are called twice, because they're nested one level and have a value
         */
        $observer->expects($this->exactly(5))
            ->method('onTagData');
        $this->saxParser->parse($this->xml);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testParseException()
    {
        $this->saxParser->parse(null);
    }

    public function testParse()
    {
        $observer = new Observer();
        $this->saxParser->dispatcher->addSubscriber($observer);
        $this->saxParser->parse($this->xml);

        // Checking if node names have been parsed
        $this->assertArrayHasKey('ROOT', $observer->data);
        $this->assertArrayHasKey('NAME', $observer->data);
        $this->assertArrayHasKey('LASTNAME', $observer->data);

        // Checking if values have been parsed
        $this->assertSame('Foo', $observer->data['NAME']);
        $this->assertSame('Bar', $observer->data['LASTNAME']);
    }

    public function testConstruct()
    {
        $this->assertEquals('xml', get_resource_type($this->saxParser->resParser));
    }

    public function testDestruct()
    {
        $this->saxParser->__destruct();
        $this->assertSame('Unknown', get_resource_type($this->saxParser->resParser));
    }
}

class Observer implements EventSubscriberInterface
{
    public $data = array();

    public static function getSubscribedEvents()
    {
        return array(
            "tag.open" => array("onTagOpen", 0),
            "tag.data" => array("onTagData", 0),
            "tag.close" => array("onTagClose", 0)
        );
    }

    public function onTagOpen($event) {}

    public function onTagData($event)
    {
        if(!array_key_exists($event['tagName'], $this->data)) {
            $this->data[$event['tagName']] = trim($event['data']);
        }
        else {
            $this->data[$event['tagName']] .= trim($event['data']);
        }
    }

    public function onTagClose($event) {}
}
