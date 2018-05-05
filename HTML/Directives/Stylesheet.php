<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Directives;

use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Parser;
use Gregwar\RST\Directive;

/**
 * Adds a stylesheet to a document, example:
 *
 * .. stylesheet:: style.css
 */
class Stylesheet extends Directive
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'stylesheet';
    }

    /**
     * @param Parser $parser
     * @param Node $node
     * @param string $variable
     * @param string $data
     * @param array $options
     */
    public function process(Parser $parser, ?Node $node, string $variable, string  $data, array $options): void
    {
        $document = $parser->getDocument();

        $document->addCss($data);

        if ($node) {
            $document->addNode($node);
        }
    }
}
