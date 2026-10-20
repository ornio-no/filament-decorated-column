<?php

namespace Ornio\FilamentDecoratedColumn\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Ornio\FilamentDecoratedColumn\Decoration;

/**
 * The prefix / suffix slots of DecoratedColumn and DecoratedEntry
 * (prefixBadges, suffixBadges, asPills, separator), holding Badge and
 * Icon decorations; prefixDecorations() and suffixDecorations() are
 * the same slots under a neutral name.
 *
 * separator() never touches the parent's list-state separator, so a
 * value containing the separator character is not split into several
 * items.
 */
trait HasDecorations
{
    /**
     * @var iterable<Decoration>|Closure
     */
    protected iterable | Closure $prefixDecorations = [];

    /**
     * @var iterable<Decoration>|Closure
     */
    protected iterable | Closure $suffixDecorations = [];

    protected bool | Closure $asPills = false;

    protected string | Closure | null $decorationSeparator = null;

    /**
     * @param  iterable<Decoration>|Closure  $decorations
     */
    public function prefixDecorations(iterable | Closure $decorations): static
    {
        $this->prefixDecorations = $decorations;

        return $this;
    }

    /**
     * @param  iterable<Decoration>|Closure  $decorations
     */
    public function suffixDecorations(iterable | Closure $decorations): static
    {
        $this->suffixDecorations = $decorations;

        return $this;
    }

    /**
     * @param  iterable<Decoration>|Closure  $badges
     */
    public function prefixBadges(iterable | Closure $badges): static
    {
        return $this->prefixDecorations($badges);
    }

    /**
     * @param  iterable<Decoration>|Closure  $badges
     */
    public function suffixBadges(iterable | Closure $badges): static
    {
        return $this->suffixDecorations($badges);
    }

    public function asPills(bool | Closure $condition = true): static
    {
        $this->asPills = $condition;

        return $this;
    }

    public function shouldBePills(): bool
    {
        return (bool) $this->evaluate($this->asPills);
    }

    /**
     * The mark between the text and a non-empty slot (default an em
     * dash). A blank separator hides it.
     */
    public function separator(string | Closure | null $separator = ','): static
    {
        $this->decorationSeparator = $separator;

        return $this;
    }

    public function getDecorationSeparator(): string
    {
        return $this->evaluate($this->decorationSeparator) ?? '&mdash;';
    }

    /**
     * Filament 5.10 added $state and $relatedRecord to the parent and
     * branches on func_num_args(), so the arguments are forwarded exactly
     * as received. Earlier 5.x versions take none and ignore them.
     */
    public function getPrefix(mixed $state = null, ?Model $relatedRecord = null): string | Htmlable | null
    {
        $html = $this->renderDecorations($this->prefixDecorations);

        if ($html === '') {
            return parent::getPrefix(...func_get_args());
        }

        return new HtmlString(
            '<span style="display:inline-flex;align-items:center;gap:0.375rem;margin-inline-end:' . $this->getDecorationSpacing() . ';vertical-align:middle;">' . $html . '</span>'
            . $this->separatorHtml() . ' '
            . $this->affixToHtml(parent::getPrefix(...func_get_args())),
        );
    }

    public function getSuffix(mixed $state = null, ?Model $relatedRecord = null): string | Htmlable | null
    {
        $html = $this->renderDecorations($this->suffixDecorations);

        if ($html === '') {
            return parent::getSuffix(...func_get_args());
        }

        return new HtmlString(
            $this->affixToHtml(parent::getSuffix(...func_get_args()))
            . ' ' . $this->separatorHtml()
            . '<span style="display:inline-flex;align-items:center;gap:0.375rem;margin-inline-start:' . $this->getDecorationSpacing() . ';vertical-align:middle;">' . $html . '</span>',
        );
    }

    public function getPrefixBadges(): string
    {
        return $this->renderDecorations($this->prefixDecorations);
    }

    public function getSuffixBadges(): string
    {
        return $this->renderDecorations($this->suffixDecorations);
    }

    /**
     * @param  iterable<Decoration>|Closure  $decorations
     */
    protected function renderDecorations(iterable | Closure $decorations): string
    {
        $html = '';

        foreach ($this->evaluate($decorations) ?? [] as $decoration) {
            $decoration->column($this);

            if ($decoration->isHidden()) {
                continue;
            }

            $html .= Str::of($decoration->render()->render())
                ->replace('<!-- __BLOCK__ --> ', '')
                ->replace('<!-- __ENDBLOCK__ -->', '')
                ->replace('<!--[if BLOCK]><![endif]-->', '')
                ->replace('<!--[if ENDBLOCK]><![endif]-->', '')
                ->trim();
        }

        return $html;
    }

    private function separatorHtml(): string
    {
        $separator = $this->getDecorationSeparator();

        return blank($separator) ? '' : '<span style="opacity:0.375;">' . $separator . '</span>';
    }

    /**
     * The gap between a slot and the text: tight beside a separator
     * (the separator carries the spacing), wider without one.
     */
    private function getDecorationSpacing(): string
    {
        return blank($this->getDecorationSeparator()) ? '0.5rem' : '0.25rem';
    }

    private function affixToHtml(string | Htmlable | null $affix): string
    {
        return $affix instanceof Htmlable ? $affix->toHtml() : e((string) $affix);
    }
}
