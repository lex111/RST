<?php

declare(strict_types=1);

namespace Gregwar\RST\Nodes;

abstract class CodeNode extends BlockNode
{
    /** @var bool */
    protected $raw = false;
    /** @var string|null */
    protected $language = null;

    public function setLanguage(string $language = null)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param bool $raw
     */
    public function setRaw(bool $raw): void
    {
        $this->raw = $raw;
    }
}
