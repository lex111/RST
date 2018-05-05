<?php

declare(strict_types=1);

namespace Gregwar\RST\Directives;

use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Nodes\TitleNode;
use Gregwar\RST\Span;
use Gregwar\RST\Parser;
use Gregwar\RST\Directive;

/**
 * This sets a new target for a following title, this can be used to change
 * its link
 */
class RedirectionTitle extends Directive
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'redirection-title';
    }

    /**
     * @param Parser $parser
     * @param Node $node
     * @param string $variable
     * @param string $data
     * @param array $options
     */
    public function process(Parser $parser, ?Node $node, string $variable, string $data, array $options): void
    {
        $document = $parser->getDocument();

        if ($node) {
            if ($node instanceof TitleNode) {
                $node->setTarget($data);
            }
            $document->addNode($node);
        }
    }
}
