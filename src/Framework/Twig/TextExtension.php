<?php

namespace Framework\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

/**
 * extensions for texte
 *
 */
class TextExtension extends AbstractExtension
{
    /**
     *
     * @return \TwigFilters[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt'])
        ];
    }

    /**
     * return extract of content with maxlegnth
     *
     * @param string $content
     * @param integer $maxLength
     * @return string
     */
    public function excerpt(string $content, int $maxLength = 100): string
    {
        if (mb_strlen($content) > $maxLength) {
            $excerpt = mb_substr($content, 0, $maxLength);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . ' ...';
        }
        return $content;
    }
}
