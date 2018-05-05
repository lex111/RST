<?php

declare(strict_types=1);

namespace Gregwar\RST\Nodes;

class WrapperNode extends Node
{
    /** @var Node */
    protected $node;
    /** @var string */
    protected $before;
    /** @var string */
    protected $after;

    /**
     * WrapperNode constructor.
     *
     * @param Node|null $node
     * @param string $before
     * @param string $after
     */
    public function __construct(?Node $node, string $before = '', string $after = '')
    {
        $this->node = $node;
        $this->before = $before;
        $this->after = $after;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $contents = $this->node ? $this->node->render() : '';

        return $this->before . $contents . $this->after;
    }
}
