<?php

declare(strict_types=1);

namespace Gregwar\RST\Nodes;

class DummyNode extends Node
{
    /** @var mixed|null */
    public $data;

    /**
     * DummyNode constructor.
     *
     * @param mixed|null $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return '';
    }
}
