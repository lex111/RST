<?php

declare(strict_types=1);

namespace Gregwar\RST\Directives;

use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Parser;
use Gregwar\RST\Directive;

/**
 * The Replace directive will set the variables for the spans
 *
 * .. |test| replace:: The Test String!
 */
class Replace extends Directive
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'replace';
    }

    /**
     * @param Parser $parser
     * @param string $variable
     * @param string|array $data
     * @param array $options
     *
     * @return Node
     */
    public function processNode(Parser $parser, string $variable, $data, array $options): Node
    {
        return $parser->createSpan($data);
    }
}
