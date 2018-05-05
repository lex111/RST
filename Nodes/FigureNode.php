<?php

declare(strict_types=1);

namespace Gregwar\RST\Nodes;

abstract class FigureNode extends Node
{
    /** @var ImageNode */
    protected $image;
    /** @var Node|null */
    protected $document;

    /**
     * FigureNode constructor.
     *
     * @param ImageNode $image
     * @param Node|null $document
     */
    public function __construct(ImageNode $image, Node $document = null)
    {
        $this->image = $image;
        $this->document = $document;
    }
}
