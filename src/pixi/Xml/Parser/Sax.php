<?php
/**
 * This class was written by the developers
 * of the pixi* Software GmbH
 *
 * For detailed information check out the repository, wiki,
 * or technical documentation
 */

namespace Pixi\Xml\Parser;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Simple API for XML (SAX) for parsing Xml data.
 * This class parses xml data using listeners for additional
 * modification of the data beeing parsed.
 *
 * It's written to handle streams and complete Xml structures.
 *
 *      $saxParser = new pixi\Xml\Parser\Sax();
 *      $saxParser ->dispatcher->addSubscriber(new Your\Own\Listener());
 *
 *      while(!feof($fp)) {
 *          $saxParser ->parse(fread($fp), 4096);
 *      }
 *
 * In this example you're initializing the parser and add a Subscriber
 * to it and finally start parsing the xml in a file stream.
 *
 * @author Silvester Maraz
 * @author Florian Seidl
 * @package pixi\Xml\Parser
 * @uses Symfony\Component\EventDispatcher\EventDispatcher
 * @link https://bitbucket.org/pixi_software/lib-xml/wiki/Home Wiki
 * @copyright pixi* Software GmbH
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
     * @return void
     */
    public function __destruct()
    {
        return xml_parser_free($this->resParser);
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
            die (sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($this->resParser)), xml_get_current_line_number($this->resParser)));
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
