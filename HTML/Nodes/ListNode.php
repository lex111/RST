<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Nodes;

use Gregwar\RST\Nodes\ListNode as Base;

class ListNode extends Base
{
    /**
     * @param string $text
     * @param string $prefix
     *
     * @return string
     */
    protected function createElement(string $text, string $prefix): string
    {
        $class = '';
        if ($prefix == '-') {
            $class = ' class="dash"';
        }

        return '<li' . $class . '>' . $text . '</li>';
    }

    /**
     * @param bool $ordered
     *
     * @return array
     */
    protected function createList(bool $ordered): array
    {
        $keyword = $ordered ? 'ol' : 'ul';

        return array('<'.$keyword.'>', '</'.$keyword.'>');
    }
}
