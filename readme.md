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

> **NOTE:** For more details visit the library [wiki](https://bitbucket.org/pixi_software/lib-xml/wiki/Home)