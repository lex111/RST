<?php

declare(strict_types=1);

namespace Gregwar\RST\Directives;

use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Parser;
use Gregwar\RST\Directive;

use Gregwar\RST\Nodes\WrapperNode;
use Gregwar\RST\Nodes\CodeNode;

/**
 * Renders a raw block, example:
 *
 * .. raw::
 *
 *      <u>Undelined!</u>
 */
class Raw extends Directive
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'raw';
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
        if ($node) {
            $kernel = $parser->getKernel();

            if ($node instanceof CodeNode) {
                $node->setRaw(true);
            }

            if ($variable) {
                $environment = $parser->getEnvironment();
                $environment->setVariable($variable, $node);
            } else {
                $document = $parser->getDocument();
                $document->addNode($node);
            }
        }
    }

    /**
     * @return bool
     */
    public function wantCode(): bool
    {
        return true;
    }
}
