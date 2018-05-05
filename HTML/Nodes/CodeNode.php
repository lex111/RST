<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Nodes;

use Gregwar\RST\Nodes\CodeNode as Base;

class CodeNode extends Base
{
    /**
     * @return string
     */
    public function render(): string
    {
        if ($this->raw) {
            return $this->value;
        } else {
            return "<pre><code class=\"".$this->language."\">".htmlspecialchars($this->value)."</code></pre>";
        }
    }
}
