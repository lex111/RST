<?php

namespace Gregwar\RST\Directives;

use Gregwar\RST\Directive;
use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Parser;

class Toctree extends Directive
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'toctree';
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
        $environment = $parser->getEnvironment();
        $kernel = $parser->getKernel();
        $files = array();

        foreach (explode("\n", $node->getValue()) as $file) {
            $file = trim($file);
            if ($file) {
                $environment->addDependency($file);
                $files[] = $file;
            }
        }

        $document = $parser->getDocument();
        $document->addNode($kernel->build('Nodes\TocNode', $files, $environment, $options));
    }

    /**
     * @return bool
     */
    public function wantCode(): bool
    {
        return true;
    }
}
