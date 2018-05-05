<?php

declare(strict_types=1);

namespace Gregwar\RST\Directives;

use Gregwar\RST\Parser;
use Gregwar\RST\Directive;
use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Nodes\DocumentNode;

/**
 * Tell that this is a document, in the case of LaTeX for instance,
 * this will mark the current document as one of the master document that
 * should be compiled
 */
class Document extends Directive
{
    public function getName(): string
    {
        return 'document';
    }

    /**
     * @param Parser $parser
     * @param string $variable
     * @param string $data
     * @param array $options
     *
     * @return Node|DocumentNode|null
     */
    public function processNode(Parser $parser, string $variable, string $data, array $options): Node
    {
        return new DocumentNode;
    }
}
