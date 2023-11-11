<?php

namespace OffByN\ShortcodeTemplates;

interface ParsedChunk
{
    public function isShortcode() : bool;
    public function asShortcode() : ?ParsedShortcode;
    public function asTextChunk() : ?TextChunk;
}
