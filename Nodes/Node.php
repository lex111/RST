<?php

declare(strict_types=1);

namespace Gregwar\RST\Nodes;

abstract class Node
{
    /** @var mixed|string|null */
    protected $value;

    /**
     * Node constructor.
     *
     * @param mixed|null $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed|null $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    abstract public function render(): string;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
