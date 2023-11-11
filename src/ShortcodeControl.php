<?php

namespace OffByN\ShortcodeTemplates;

class ShortcodeControl
{
    public bool $close;
    public string $name;
    public ?string $value;
    public array $attributes;
    public int $start;
    public int $length;

    public function __construct(
        bool $close,
        string $name,
        ?string $value,
        array $attributes,
        int $start,
        int $length
    )
    {
        $this->close = $close;
        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
        $this->start = $start;
        $this->length = $length;
    }
}
