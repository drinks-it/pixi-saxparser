<?php

namespace Example\Subscriber;

/**
 * Example FileWriter, which can be used as subscriber to the
 * xml parser class.
 */
class FileWriter implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    protected $fp;
    protected $filename;
    protected $content;

    /**
     * Mandatory method, which tells the dispatcher, which events
     * this subsriber is listening to and what method in the
     * subscriber will be called. Additionally defines the priority
     * of the subscriber methods.
     *
     * @access public
     * @param void
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            "tag.open" => array("onTagOpen", 0),
            "tag.data" => array("onTagData", 0),
            "tag.close" => array("onTagClose", 0)
        );
    }

    /**
     * Sets the filename property
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * This method adds some additional information
     * to the data and creates a string, which will be
     * written to the file.
     *
     * @access public
     * @param array $event
     * @return void
     */
    public function onTagOpen($event)
    {
        switch($event['tagName']) {
            case 'BREAKFAST_MENU':
                $this->fp = fopen($this->filename, 'w');
                $this->content = '';
                break;
            case 'PRICE':
                $this->content .= ' costs ';
                break;
            case 'CALORIES':
                $this->content .= 'They have ';
                break;
            default:
                $this->content .= '';
                break;
        }
    }

    /**
     * Is executed when the data of an xml tag is parsed.
     * It just trims the data, without further modification.
     *
     * @access public
     * @param array $event
     * @return void
     */
    public function onTagData($event)
    {
        $this->content .= trim($event['data']);
    }

    /**
     * This method adds some additional information
     * to the data and creates a string, which will be
     * written to the file.
     *
     * @access public
     * @param array $event
     * @return void
     */
    public function onTagClose($event)
    {
        switch ($event['tagName']) {
            case 'PRICE':
            case 'DESCRIPTION':
                $this->content .= '. ';
                break;
            case 'CALORIES':
                $this->content .= ' calories.';
                break;
            case 'FOOD':
                $this->content .= "\n";
                break;
            case 'BREAKFAST_MENU':
                fwrite($this->fp, $this->content);
                fclose($this->fp);
                return;
                break;
            default:
                $this->content .= '';
                break;
        }
    }
}
