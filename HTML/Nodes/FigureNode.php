<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Nodes;

use Gregwar\RST\Nodes\FigureNode as Base;

class FigureNode extends Base
{
    /**
     * @return string
     */
    public function render(): string
    {
        $html = '<figure>';
        $html .= $this->image->render();
        if ($this->document) {
            $caption = trim($this->document->render());
            if ($caption) {
                $html .= '<figcaption>'.$caption.'</figcaption>';
            }
        }
        $html .= '</figure>';

        return $html;
    }
}
