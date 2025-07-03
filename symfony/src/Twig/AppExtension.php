<?php

namespace App\Twig;

use Recurr\Rule;
use Recurr\Transformer\TextTransformer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('nl2p', [$this, 'paragraphs']),
            new TwigFilter('rrule', [$this, 'rrule']),
        ];
    }

    public function rrule($string): string
    {
        $rule = new Rule($string);
        $transformer = new TextTransformer();
        return $transformer->transform($rule);
    }

    public function paragraphs($string): string
    {
        $paragraphs = '';

        foreach (explode("\n", $string) as $line) {
            if (trim($line)) {
                $paragraphs .= '<p>' . $line . '</p>';
            }
        }

        return $paragraphs;
    }
}
