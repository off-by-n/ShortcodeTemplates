# Shortcode Templates

This library offers a simple parser for text containing "shortcodes", the underlying format of [BBCode](https://en.wikipedia.org/wiki/BBCode).

The scope of this project is to turn plain text containing generic shortcodes into a corresponding data structure. This should be as fast and efficient as possible, even for large amounts of text and deeply nested structures. 
It is not within the scope of this project to support any processing beyond that. 
For a more flexible solution that is optimized for smaller amounts of text, see [Shortcode](https://github.com/thunderer/Shortcode).


## Installation

```
composer require off-by-n/shortcode-templates
```

## Usage

```php
(new ShortcodeParser)->parse($inputString);
```

Example input string:

```
Lorem ipsum dolor sit amet, consetetur sadipscing elitr, [hr bold][color=red]sed diam nonumy eirmod [color="green"]tempor invidunt ut labore et[/color] dolore [url="https://example.org" alt="Magna"]magna[/url] aliquyam [/color] erat, [b]sed[/b] diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
```

This would produce the equivalent of the following data structure. As you can see, both closed and unclosed shortcodes are allowed. The plain text inbetween shortcodes is part of the output:

```json
[
    "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, ",
    {
        "name": "hr",
        "value": null,
        "attributes": {
            "bold": true
        },
        "children": null
    },
    {
        "name": "color",
        "value": "red",
        "attributes": {},
        "children": [
            "sed diam nonumy eirmod ",
            {
                "name": "color",
                "value": "green",
                "attributes": {},
                "children": [
                    "tempor invidunt ut labore et"
                ]
            },
            " dolore ",
            {
                "name": "url",
                "value": "https:\/\/example.org",
                "attributes": {
                    "alt": "Magna"
                },
                "children": [
                    "magna"
                ]
            },
            " aliquyam "
        ]
    },
    " erat, ",
    {
        "name": "b",
        "value": null,
        "attributes": {},
        "children": [
            "sed"
        ]
    },
    " diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet."
]
```

You can then roll your own logic to proceed. Basic example:

```php
function process($chunks = [])
{
    $output = '';
    foreach ($chunks as $chunk) {
        if ($chunk->isShortcode()) {
            $shortcode = $chunk->asShortcode();
            if ($shortcode->getName() == 'raw') {
                $output .= $shortcode->getInnerSource();
            } else {
                $output .= process($shortcode->getChildren());
            }
        } else {
            $output .= $chunk;
        }
    }

    return $output;
}
```

## Properties

The anatomy of a shortcode is assumed to be:

`[<name>=<value> <attributeName1>=<attributeValue1> <attributeNameN>=<attributeValueN>]<children>[/<name>]`

All parts but the opening name and brackets are optional. 
The following methods are available for each shortcode:
- `getName()` - retrieves the shortcode's name
- `getValue()` - retrieves the shortcode's value, or `null` if none provided
- `getAttributes()` - retrieves all attributes as an associative array
- `getAttribute($name)` - retrieves a single attribute, by name, or `null` if not defined
- `getChildren()` - retrieves an array of all child chunks, or `null` for unclosed shortcodes
- `getInnerSource()` - retrieves the unparsed source text between the shortcode's opening and closing tag or `null` for unclosed shortcodes
- `getOuterSource()` - retrieves the unparsed source text of the shortcode, including opening and closing tag

All chunks, including text chunks, have a method `isShortcode()` to differentiate between types of chunk: `shortcode` or `text`.

## Why RegEx

By default, this library uses PHP's own [RegEx](https://en.wikipedia.org/wiki/Regular_expression) extension to search the source text for control structures. The reasons for that are performance and easy setup: The Shortcodes format is made up of large amounts of literal text containing few relatively short control structures of a known format. The RegEx extension is optimized to find exactly that. Since it is a native implementation, it will usually have a better performance than any solution built in PHP code. Because the extension is already present in most installations, no additional extensions are required to install this parser. If another implementation is needed, it should be relatively easy to add.
