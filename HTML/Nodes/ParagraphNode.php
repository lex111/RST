<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Nodes;

use Gregwar\RST\Nodes\ParagraphNode as Base;

class ParagraphNode extends Base
{
    /**
     * @return string
     */
    public function render(): string
    {
        $text = (string) $this->value;

        if (trim($text)) {
            return '<p>'.$text.'</p>';
        } else {
            return '';
        }
    }
}
