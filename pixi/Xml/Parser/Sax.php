<?php

namespace pixi\Xml\Parser;

use Symfony\Component\EventDispatcher\EventDispatcher;

class Sax
{
    public $dispatcher;    
    
    public $resParser;
    public $strXmlData;
    public $lastOpenTag;

    public function __construct()
    {
        $this->resParser = xml_parser_create();
        xml_set_object($this->resParser, $this);
        xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
        xml_set_character_data_handler($this->resParser, "tagData");
        
        $this->dispatcher = new EventDispatcher();
    }

    public function __destruct()
    {
        xml_parser_free($this->resParser);
    }

    public function parse($strInputXML)
    {
        $this->strXmlData = xml_parse($this->resParser, $strInputXML);

        if(!$this->strXmlData) {
            die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($this->resParser)), xml_get_current_line_number($this->resParser)));
        }
    }

    public function tagOpen($parser, $name, $attrs)
    {
        $this->lastOpenTag = $name;
        $this->dispatcher->dispatch("tag.open", new \Symfony\Component\EventDispatcher\GenericEvent("sax.parser", array("tagName"  => $name, "data" => $attrs)));   
    }

    public function tagData($parser, $tagData)
    {

        $this->dispatcher->dispatch("tag.data", new \Symfony\Component\EventDispatcher\GenericEvent("sax.parser", array("tagName"  => $this->lastOpenTag, "data" => $tagData)));
        
    }

    public function tagClosed($parser, $name)
    {
        $this->dispatcher->dispatch("tag.close", new \Symfony\Component\EventDispatcher\GenericEvent("sax.parser", array("tagName"  => $name, "data" => "")));   
    }
}
