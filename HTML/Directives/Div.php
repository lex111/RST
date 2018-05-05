<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Directives;

use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;
use Gregwar\RST\Nodes\WrapperNode;

/**
 * Divs a sub document in a div with a given class
 */
class Div extends SubDirective
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'div';
    }

    /**
     * @param Parser $parser
     * @param Node $document
     * @param string $variable
     * @param string $data
     * @param array $options
     *
     * @return WrapperNode
     */
    public function processSub(Parser $parser, ?Node $document, string $variable, string $data, array $options): Node
    {
        return new WrapperNode($document, '<div class="'.$data.'">', '</div>');
    }
}
