<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Nodes;

use Gregwar\RST\Nodes\SeparatorNode as Base;

class SeparatorNode extends Base
{
    /**
     * @return string
     */
    public function render(): string
    {
        return '<hr />';
    }
}
