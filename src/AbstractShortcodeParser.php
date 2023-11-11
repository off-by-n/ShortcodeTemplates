<?php

namespace OffByN\ShortcodeTemplates;

abstract class AbstractShortcodeParser
{
    /**
     * @return ParsedChunk[]
     */
    public function parse(string $source)
    {
        $controls = $this->extractShortcodeControls($source);

        /** @var ParsedChunk[] */
        $chunks = [];
        $lastControlEnd = 0;
        foreach ($controls as $control) {
            if ($control->start > $lastControlEnd) {
                $chunks[] = new TextChunk(
                    new TextReference(
                        $source,
                        $lastControlEnd,
                        $control->start - $lastControlEnd
                    )
                );
            }

            if ($control->close) {
                $hasMatchingOpener = false;
                for ($i = count($chunks) - 1; $i >= 0; $i--) {
                    $candidate = $chunks[$i]->asShortcode();
                    if (
                        $candidate !== null
                        && $candidate->getName() == $control->name
                        && $candidate->getInnerSource() === null
                    ) {
                        $opener = $candidate;
                        $openerSource = $opener->getOuterSource();
                        $openerSourceEnd = $openerSource->getStart() + $openerSource->getLength();
                        $children = array_splice($chunks, $i + 1, count($chunks) - $i);
                        $chunks[$i] = new ParsedShortcode(
                            $opener->getName(),
                            $opener->getValue(),
                            $opener->getAttributes(),
                            new TextReference(
                                $source,
                                $openerSource->getStart(),
                                ($control->start + $control->length) - $openerSource->getStart()
                            ),
                            new TextReference(
                                $source,
                                $openerSourceEnd,
                                $control->start - $openerSourceEnd
                            ),
                            $children
                        );
                        $hasMatchingOpener = true;
                        break;
                    }
                }

                if (!$hasMatchingOpener) {
                    $chunks[] = new TextChunk(new TextReference($source, $control->start, $control->length));
                }
            } else {
                $chunks[] = new ParsedShortcode(
                    $control->name,
                    $control->value,
                    $control->attributes,
                    new TextReference(
                        $source,
                        $control->start,
                        $control->length
                    )
                );
            }

            $lastControlEnd = $control->start + $control->length;
        }

        $sourceLength = strlen($source);
        if ($lastControlEnd < $sourceLength) {
            $chunks[] = new TextChunk(
                new TextReference($source, $lastControlEnd, $sourceLength - $lastControlEnd)
            );
        }

        return $chunks;
    }

    /**
     * @return ShortcodeControl[]
     */
    protected abstract function extractShortcodeControls(string $source);
}
