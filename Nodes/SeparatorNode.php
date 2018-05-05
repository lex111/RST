<?php

declare(strict_types=1);

namespace Gregwar\RST\Nodes;

abstract class SeparatorNode extends Node
{
    /** @var int */
    protected $level;

    /**
     * SeparatorNode constructor.
     *
     * @param int $level
     */
    public function __construct(int $level)
    {
        $this->level = $level;
    }
}
