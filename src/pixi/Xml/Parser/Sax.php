<?php

namespace pixi\Xml\Parser;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Silvester Maraz
 * @author Florian Seidl
 */
class Sax
{
    /**
     * Property that contains the Symfony2 EventDispatcher.
     * You can use this object directly to add listeners or subscribers.
     * E.g. $sax->dispatcher->addSubscriber();
     *
     * @var EventDispatcher
     * @access public
     */
    public $dispatcher;

    /**
     * Contains the resource handle for the new XML parser
     *
     * @var resource handle
     * @access public
     */
    public $resParser;

    /**
     * Contains the current line, which will be parsed by the sax parser
     *
     * @var string
     * @access public
     */
    public $strXmlData;

    /**
     * Contains the last opened tag, which will be passed as argument to
     * the event "tag.data".
     *
     * @var string
     * @access public
     */
    public $lastOpenTag;

    /**
     * Constructor
     *
     * Initializes the sax parser and initializes a Symfony2
     * EventDispatcher object.
     *
     * @param void
     * @return void
     * @access public
     */
    public function __construct()
    {
        $this->resParser = xml_parser_create();
        xml_set_object($this->resParser, $this);
        xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
        xml_set_character_data_handler($this->resParser, "tagData");

        $this->dispatcher = new EventDispatcher();
    }

    /**
     * Destructor
     *
     * Frees the current XML parser
     *
     * @access public
     * @param void
     * @return void
     */
    public function __destruct()
    {
        xml_parser_free($this->resParser);
    }

    /**
     * Parse
     *
     * Parsing the given xml or string.
     *
     * @access public
     * @param string $strInputXML
     * @return void
     */
    public function parse($strInputXML)
    {
        $this->strXmlData = xml_parse($this->resParser, $strInputXML);

        if(!$this->strXmlData) {
            die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($this->resParser)), xml_get_current_line_number($this->resParser)));
        }
    }

    /**
     * TagOpen
     *
     * Is executed when parsing an xml tag, which is opened.
     * This method is connected to the event 'tag.open', which
     * can be used by an listener or subscriber.
     *
     * @access public
     * @param XmlParser $parser
     * @param string $name
     * @param string $attrs
     * @return void
     */
    public function tagOpen($parser, $name, $attrs)
    {
        $this->lastOpenTag = $name;
        $this->dispatcher->dispatch("tag.open", new \Symfony\Component\EventDispatcher\GenericEvent("sax.parser", array("tagName"  => $name, "data" => $attrs)));
    }

    /**
     * TagData
     *
     * Is executed when parsing an xml tag content.
     * This method is connected to the event 'tag.data', which
     * can be used by an listener or subscriber.
     *
     * @access public
     * @param XmlParser $parser
     * @param string $tagData
     * @return void
     */
    public function tagData($parser, $tagData)
    {

        $this->dispatcher->dispatch("tag.data", new \Symfony\Component\EventDispatcher\GenericEvent("sax.parser", array("tagName"  => $this->lastOpenTag, "data" => $tagData)));

    }

    /**
     * TagClose
     *
     * Is executed when parsing an xml tag, which is closed.
     * This method is connected to the event 'tag.close', which
     * can be used by an listener or subscriber.
     *
     * @access public
     * @param XmlParser $parser
     * @param string $name
     * @return void
     */
    public function tagClosed($parser, $name)
    {
        $this->dispatcher->dispatch("tag.close", new \Symfony\Component\EventDispatcher\GenericEvent("sax.parser", array("tagName"  => $name, "data" => "")));
    }
}
