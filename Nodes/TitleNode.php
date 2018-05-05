<?php

declare(strict_types=1);

namespace Gregwar\RST\Nodes;

abstract class TitleNode extends Node
{
    /** @var int */
    protected $level;
    /** @var string */
    protected $token;
    /** @var string|null */
    protected $target = null;

    public function __construct(Node $value, int $level, string $token)
    {
        parent::__construct($value);
        $this->level = $level;
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param string $target
     */
    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }
}
