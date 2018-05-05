<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Directives;

use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;

use Gregwar\RST\HTML\Nodes\ImageNode;
use Gregwar\RST\HTML\Nodes\FigureNode;

/**
 * Renders an image, example :
 *
 * .. figure:: image.jpg
 *      :width: 100
 *      :title: An image
 *
 *      Here is an awesome caption
 *
 */
class Figure extends SubDirective
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'figure';
    }

    /**
     * @param Parser $parser
     * @param Node $document
     * @param string $variable
     * @param string $data
     * @param array $options
     *
     * @return FigureNode
     */
    public function processSub(Parser $parser, ?Node $document, string $variable, string $data, array $options): Node
    {
        $environment = $parser->getEnvironment();
        $url = $environment->relativeUrl($data);

        return new FigureNode(new ImageNode($url, $options), $document);
    }
}
