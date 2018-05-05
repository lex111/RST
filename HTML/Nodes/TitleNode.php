<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Nodes;

use Gregwar\RST\Nodes\TitleNode as Base;

class TitleNode extends Base
{
    /**
     * @return string
     */
    public function render(): string
    {
        return '<a id="'.$this->token.'"></a><h'.$this->level.'>'.$this->value.'</h'.$this->level.">";
    }
}
