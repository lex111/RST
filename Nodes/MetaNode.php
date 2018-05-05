<?php

declare(strict_types=1);

namespace Gregwar\RST\Nodes;

abstract class MetaNode extends Node
{
    /** @var string */
    protected $key;
    /** @var string */
    protected $value;

    /**
     * MetaNode constructor.
     *
     * @param string $key
     * @param string $value
     */
    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }
}
