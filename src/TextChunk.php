<?php

namespace OffByN\ShortcodeTemplates;

class TextChunk implements ParsedChunk
{
    private TextReference $source;

    public function __construct(TextReference $source)
    {
        $this->source = $source;
    }

    public function asShortcode() : ?ParsedShortcode
    {
        return null;
    }

    public function asTextChunk() : TextChunk
    {
        return $this;
    }

    public function isShortcode() : bool
    {
        return false;
    }

    public function __toString()
    {
        return $this->source->__toString();
    }
}
