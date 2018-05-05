<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Directives;

use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Parser;
use Gregwar\RST\Directive;

use Gregwar\RST\HTML\Nodes\MetaNode;

/**
 * Add a meta information:
 *
 * .. meta::
 *      :key: value
 */
class Meta extends Directive
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'meta';
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

        foreach ($options as $key => $value) {
            $meta = new MetaNode($key, $value);
            $document->addHeaderNode($meta);
        }

        if ($node) {
            $document->addNode($node);
        }
    }
}
