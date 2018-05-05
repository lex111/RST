<?php

declare(strict_types=1);

namespace Gregwar\RST\Nodes;

use Gregwar\RST\Environment;

abstract class TocNode extends Node
{
    /** @var array */
    protected $files;
    /** @var Environment */
    protected $environment;
    /** @var array */
    protected $options;

    public function __construct(array $files, Environment $environment, array $options)
    {
        $this->files = $files;
        $this->environment = $environment;
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
