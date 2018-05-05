<?php

declare(strict_types=1);

namespace Gregwar\RST\Nodes;

abstract class ImageNode extends Node
{
    /** @var string */
    protected $url;
    /** @var array */
    protected $options;

    /**
     * ImageNode constructor.
     *
     * @param string $url
     * @param array $options
     */
    public function __construct(string $url, array $options = array())
    {
        $this->url = $url;
        $this->options = $options;
    }
}
