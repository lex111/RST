<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML;

use Gregwar\RST\Span as Base;

class Span extends Base
{
    /**
     * @param string $text
     *
     * @return string
     */
    public function emphasis(string $text): string
    {
        return '<em>'.$text.'</em>';
    }

    /**
     * @param $text
     *
     * @return string
     */
    public function strongEmphasis($text): string
    {
        return '<strong>'.$text.'</strong>';
    }

    /**
     * @return string
     */
    public function nbsp(): string
    {
        return '&nbsp;';
    }

    /**
     * @return string
     */
    public function br(): string
    {
        return '<br />';
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function literal(string $text): string
    {
        return '<code>'.$text.'</code>';
    }

    /**
     * @param string $url
     * @param string $title
     *
     * @return string
     */
    public function link(string $url, string $title): string
    {
        return '<a href="'.htmlspecialchars($url).'">'.$title.'</a>';
    }

    /**
     * @param string $span
     *
     * @return string
     */
    public function escape(string $span): string
    {
        return htmlspecialchars($span);
    }

    /**
     * @param array $reference
     * @param array $value
     *
     * @return string
     */
    public function reference(array $reference, array $value): string
    {
        if ($reference) {
            $text = $value['text'] ?: (isset($reference['title']) ? $reference['title'] : '');
            $url = $reference['url'];
            if ($value['anchor']) {
                $url .= '#' . $value['anchor'];
            }
            $link = $this->link($url, trim($text));
        } else {
            $link = $this->link('#', '(unresolved reference)');
        }

        return $link;
    }
}
