<?php

declare(strict_types=1);

namespace Gregwar\RST;

abstract class Kernel
{
    /**
     * Get the name of the kernel
     */
    abstract function getName(): string;

    /**
     * Gets the class for the given name
     *
     * @param string $name
     *
     * @return string
     */
    public function getClass(string $name): string
    {
        return 'Gregwar\RST\\'.$this->getName().'\\'.$name;
    }

    /**
     * Create an instance of some class
     *
     * @param string $name
     * @param mixed|null $arg1
     * @param mixed|null $arg2
     * @param mixed|null $arg3
     * @param mixed|null $arg4
     *
     * @return object|null
     */
    public function build(string $name, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null): ?object
    {
        $class = $this->getClass($name);

        if ($class) {
            return new $class($arg1, $arg2, $arg3, $arg4);
        }

        return null;
    }

    /**
     * Gets the available directives
     */
    public function getDirectives(): array
    {
        return [
            new Directives\Dummy,
            new Directives\CodeBlock,
            new Directives\Raw,
            new Directives\Replace,
            new Directives\Toctree,
            new Directives\Document,
            new Directives\RedirectionTitle,
        ];
    }

    /**
     * Document references
     */
    public function getReferences(): array
    {
        return [
            new References\Doc,
            new References\Doc('ref'),
        ];
    }

    /**
     * Allowing the kernel to tweak document after the build
     *
     * @param \Gregwar\RST\Document $document
     */
    public function postParse(Document $document): void
    {
    }

    /**
     * Allowing the kernel to tweak the builder
     *
     * @param \Gregwar\RST\Builder $builder
     */
    public function initBuilder(Builder $builder): void
    {
    }

    /**
     * Get the output files extension
     */
    public function getFileExtension(): string
    {
        return 'txt';
    }
}
