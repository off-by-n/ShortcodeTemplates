<?php

namespace OffByN\ShortcodeTemplates;

class ParsedShortcode implements ParsedChunk
{
    private $name;
    private $value;
    private array $attributes;
    private TextReference $outerSource;
    private ?TextReference $innerSource;

    /** @var ParsedChunk[]|null $children; */
    private ?array $children;

    /**
     * @param ParsedChunk[] $children
     */
    public function __construct(
        $name,
        $value,
        array $attributes,
        TextReference $outerSource,
        ?TextReference $innerSource = null,
        array $children = null
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
        $this->outerSource = $outerSource;
        $this->innerSource = $innerSource;
        $this->children = $children;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getInnerSource()
    {
        return $this->innerSource;
    }

    public function getOuterSource()
    {
        return $this->outerSource;
    }

    public function asShortcode() : ParsedShortcode
    {
        return $this;
    }

    public function asTextChunk() : ?TextChunk
    {
        return null;
    }

    public function isShortcode() : bool
    {
        return true;
    }

    public function __toString()
    {
        return $this->outerSource->__toString();
    }
}
