<?php

namespace Framework\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class TimeExtension extends AbstractExtension
{
    /**
     * Undocumented function
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    public function ago(\Datetime $date, string $format = 'd/m/y H:i')
    {
        return $result = '<span class="timeago" datetime="' . $date->format(\Datetime::ISO8601) . '">' . $date->format($format) . '</span>';
    }
}
