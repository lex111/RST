<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Directives;

use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Parser;
use Gregwar\RST\Directive;

use Gregwar\RST\Nodes\RawNode;

/**
 * Add a meta title to the document
 *
 * .. title:: Page title
 */
class Title extends Directive
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'title';
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

        $document->addHeaderNode(new RawNode('<title>'.htmlspecialchars($data).'</title>'));

        if ($node) {
            $document->addNode($node);
        }
    }
}
