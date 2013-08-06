# Lib: XML

-----
The Xml library contains an xml parser, which is able to parse whole xml's or lines from a file stream. It uses the dispatcher component of Symfony2 which makes it easy to react numerous xml structures. As a developer you have the possibility to write your own listener, which will be added to the SAX object. That way you can exactly controll what happens with the xml during parsing.

## Quick Start

-----

To run the example application, you must first install the development dependencies via composer. From the root `Lib - XML`, run:

	$ php composer install

After including the `autoload.php` into your project you can start using the parser.
> **NOTE:** The parser wont return anything unless you've created a listener that do so.

To initialize the parser and adding a listener use following code snippet:
```
:::PHP
$saxParser = new pixi\Xml\Parser\Sax();
$saxParser ->dispatcher->addSubscriber(new Your\Own\Listener());
```

To start the parsing process you can simply run the `parse` method.
```
:::PHP
$saxParser = new pixi\Xml\Parser\Sax();
$saxParser ->dispatcher->addSubscriber(new Your\Own\Listener());

while(!feof($fp)) {
	$saxParser ->parse(fread($fp), 4096);
}
```

## Listener

-----

To take advantage of an existing event, you need to connect a listener to the parser so that it can be notified when the event is dispatched. A call to the dispatcher `addSubscriber` method associates any valid PHP callable to an event:
```
:::PHP
$streamParser = new pixi\Xml\Parser\Sax();
$streamParser->dispatcher->addSubscriber(new Your\Own\Listener());
```

Another way to add a Listener is by calling the dispatcher `addListener`method. In this case you provide the event the callable is listening to and the callable itself. It can be either a callable function or an object. When adding an object as listener you have to also define the method which should be called, when dispatching the given event.
```
:::PHP
$saxParser = new pixi\Xml\Parser\Sax();

// adds a new listener object to the dispatcher
$saxParser ->dispatcher->addListener('foo.event', array($listener, 'onFooEvent'));

// adds a new callable function to the dispatcher
$saxParser ->dispatcher->addListener('foo.event', function (Event $event) {
	// will do something when foo.event is dispatched
});
```

Once you've registered your own listener to the dispatcher of the parser, it waits until the event is notified.

**Events:** Following events are dispatched by the library

Event    |Description                       |Argument
---------|----------------------------------|-------------------------------------------------------------
tag.open |When an xml tag is being opened   |`tagName => name of the tag`, `data => attributes of the tag`
tag.data |When the content of an tag is read|`tagName => last opened tag`, `data => content of the current tag`
tag.close|When an xml tag is being closed   |`tagName => name of the tag`, `data => null`

## Using event subscriber

The most common way to listen to an event is to register an *event listener* with the dispatcher. This listener can listen to one or more events and is notified each time those events are dispatched.

Another way to listen to events is via an *event subscriber*. An event subscriber is a PHP class that's able to tell the dispatcher exactly which events it should subscribe to. It implements the `EventSubscriberInterface` interface, which requires a single static method called `staticSubscribedEvents`. Takte the following example of a subscriber that subscribes to the parser events:

```
:::PHP
namespace Example\Subscriber;

class FileWriter implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
	public static function getSubscribedEvents()
    {
        return array(
            "tag.open" => array("onTagOpen", 0),
            "tag.data" => array("onTagData", 0),
            "tag.close" => array("onTagClose", 0)
        );
    }

    public function onTagOpen($event)
    {       
        // ...
    }
    
    public function onTagData($event)
    {
        // ...
    }
    
    public function onTagClose($event)
    {
        // ...
    }        
```

This is very similar to a listener class, except that the class itself can tell the dispatcher which events it should listen to. To register a subscriber with the dispatcher inside the xml parser use the addSubscriber()` method:
```
:::PHP
use pixi\Xml\Parser;
use Example\Subscriber;

$subscriber = new FileWriter();
$saxParser = new Sax();
$saxParser->dispatcher->addSubscriber($subscriber);
```

To get more detailed information about the dispatcher component, check out the [Smyfony2 component documentation](http://symfony.com/doc/current/components/event_dispatcher/introduction.html)