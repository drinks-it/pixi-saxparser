<?php

namespace Example\Subscriber;

class FileWriter implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    protected $fp;
    protected $filename;
    protected $content;
    
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

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
    
    public function onTagData($event)
    {
        $this->content .= trim($event['data']);
    }
    
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
    
    public static function getSubscribedEvents()
    {
        return array(
            "tag.open" => array("onTagOpen", 0),
            "tag.data" => array("onTagData", 0),
            "tag.close" => array("onTagClose", 0)
        );
    }
    
    
}
