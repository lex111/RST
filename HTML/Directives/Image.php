<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Directives;

use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Parser;
use Gregwar\RST\Directive;

use Gregwar\RST\HTML\Nodes\ImageNode;

/**
 * Renders an image, example :
 *
 * .. image:: image.jpg
 *      :width: 100
 *      :title: An image
 *
 */
class Image extends Directive
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'image';
    }

    /**
     * @param Parser $parser
     * @param string $variable
     * @param string $data
     * @param array $options
     *
     * @return ImageNode
     */
    public function processNode(Parser $parser, $variable, $data, array $options): Node
    {
        $environment = $parser->getEnvironment();
        $url = $environment->relativeUrl($data);

        return new ImageNode($url, $options);
    }
}
