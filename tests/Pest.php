<?php

use Ornio\FilamentDecoratedColumn\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Parses rendered HTML, a fragment or a whole component, for XPath queries.
 * The document is $xpath->document. The encoding hint keeps characters such
 * as the minus sign intact, and libxml's warnings about HTML5 tags are
 * silenced.
 */
function htmlXPath(string $html): DOMXPath
{
    $document = new DOMDocument;
    $previous = libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="UTF-8"><div>' . $html . '</div>');
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    return new DOMXPath($document);
}

/**
 * The path of a Heroicon arrow at the small icon size ('up' or 'down'), to
 * tell which arrow a trend rendered.
 */
function trendArrow(string $direction): string
{
    preg_match('/\sd="([^"]+)"/', svg('heroicon-c-arrow-' . $direction)->contents(), $matches);

    return 'd="' . $matches[1] . '"';
}
