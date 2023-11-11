<?php

namespace OffByN\ShortcodeTemplates;

class TextReference
{
    private $sourceText;
    private int $start;
    private int $length;

    public function __construct(&$sourceText, int $start, int $length)
    {
        $this->sourceText = &$sourceText;
        $this->start = $start;
        $this->length = $length;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function __toString()
    {
        return substr($this->sourceText, $this->start, $this->length);
    }
}
