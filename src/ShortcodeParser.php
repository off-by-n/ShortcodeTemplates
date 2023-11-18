<?php

namespace OffByN\ShortcodeTemplates;

class ShortcodeParser extends AbstractShortcodeParser
{
    protected static string $shortcodeRegexTemplate = '\\[{space}(?<close>\\/)?{space}(?<name>{key})(?:{space}={space}{value})?(?<attributes>(?:\\s+(?:{key}(?:{space}={space}(?:{encodedValue}|{simpleValue}))?))+)?{space}\\]';
    protected static string $attributeRegexTemplate = '(?<=\\s)(?<name>{key})(?:{space}={space}{value})?';
    protected static string $valueRegexTemplate = '(?:(?<valueEncoded>{encodedValue})|(?<valueSimple>{simpleValue}))';
    protected static $regexParts = [
        '{key}' => '[^\\s=\\]]+',
        '{encodedValue}' => '"(?:.|\\n)*?(?<!\\\\)(?:\\\\\\\\)*"',
        '{simpleValue}' => '(?!")[^\\s\\]]+',
        '{space}' => '\\s*',
    ];

    protected string $shortcodeRegex;
    protected string $attributeRegex;

    public function __construct()
    {
        $regexParts = static::$regexParts;
        $regexParts['{value}'] = strtr(static::$valueRegexTemplate, $regexParts);
        $this->shortcodeRegex = '/' . strtr(static::$shortcodeRegexTemplate, $regexParts) . '/';
        $this->attributeRegex = '/' . strtr(static::$attributeRegexTemplate, $regexParts) . '/';
    }

    protected function extractShortcodeControls(string $source)
    {
        $matches = [];
        preg_match_all($this->shortcodeRegex, $source, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        $controls = [];
        foreach ($matches as $match) {
            $controls[] = new ShortcodeControl(
                $match['close'][1] >= 0,
                $match['name'][0],
                $this->parseValue($match),
                isset($match['attributes']) ? 
                    $this->parseAttributes($match['attributes'][0]) : [],
                $match[0][1],
                strlen($match[0][0])
            );
        }
        return $controls;
    }

    protected function parseAttributes($attributesString)
    {
        $attributes = [];
        $matches = [];
        preg_match_all($this->attributeRegex, $attributesString, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        foreach ($matches as $match) {
            $name = $match['name'][0];
            $value = $this->parseValue($match) ?? true;
            $attributes[$name] = $value;
        }

        return $attributes;
    }

    protected function parseValue($match)
    {
        if (isset($match['valueEncoded']) && $match['valueEncoded'][1] >= 0) {
            return $this->parseEncodedValue($match['valueEncoded'][0]);
        } elseif (isset($match['valueSimple']) && $match['valueSimple'][1]) {
            return $match['valueSimple'][0];
        } else {
            return null;
        }
    }

    protected function parseEncodedValue($value)
    {
        // ensure characters are properly encoded
        $value = preg_replace_callback(
            '/[^ -~]+/',
            function ($match) {
                return substr(json_encode($match[0]), 1, -1);
            },
            $value
        );

        return json_decode($value);
    }
}
