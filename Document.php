<?php

declare(strict_types=1);

namespace Gregwar\RST;

use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Nodes\TitleNode;
use Gregwar\RST\Nodes\TocNode;
use Gregwar\RST\Nodes\RawNode;

abstract class Document extends Node
{
    /** @var Environment */
    protected $environment;
    /** @var array */
    protected $headerNodes = [];
    /** @var Node[] */
    protected $nodes = [];

    /**
     * Document constructor.
     *
     * @param Environment $environment
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return Environment
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    /**
     * @return string
     */
    public function renderDocument(): string
    {
        return $this->render();
    }

    /**
     * Getting all nodes of the document that satisfies the given
     * function. If the function is null, all the nodes are returned.
     *
     * @param callable|null $function
     *
     * @return Node[]
     */
    public function getNodes(callable $function = null): array
    {
        $nodes = [];

        if ($function == null) {
            return $this->nodes;
        }

        foreach ($this->nodes as $node) {
            if ($function($node)) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }

    /**
     * Gets the main title of the document
     */
    public function getTitle(): ?string
    {
        foreach ($this->nodes as $node) {
            if ($node instanceof TitleNode && $node->getLevel() == 1) {
                return $node->getValue().'';
            }
        }

        return null;
    }

    /**
     * Get the table of contents of the document
     */
    public function getTocs(): array
    {
        $tocs = [];

        $nodes = $this->getNodes(function($node) {
            return $node instanceof TocNode;
        });

        /** @var TocNode $toc */
        foreach ($nodes as $toc) {
            $files = $toc->getFiles();

            foreach ($files as &$file) {
                $file = $this->environment->canonicalUrl($file);
            }

            $tocs[] = $files;
        }

        return $tocs;
    }

    /**
     * Gets the titles hierarchy in arrays, for instance :
     *
     * array(
     *     array('Main title', array(
     *         array('Sub title', array()),
     *         array('Sub title 2', array(),
     *         array(array('Redirection', 'target'), array(),
     *     )
     * )
     */
    public function getTitles(): array
    {
        $titles = [];
        $levels = [&$titles];

        foreach ($this->nodes as $node) {
            if ($node instanceof TitleNode) {
                $level = $node->getLevel();
                $text = (string)$node->getValue();
                $redirection = $node->getTarget();
                $value = $redirection ? [$text, $redirection] : $text;

                if (isset($levels[$level-1])) {
                    $parent = &$levels[$level-1];
                    $element = [$value, []];
                    $parent[] = $element;
                    $levels[$level] = &$parent[count($parent)-1][1];
                }
            }
        }

        return $titles;
    }

    /**
     * @param Node|string $node
     */
    public function addNode($node): void
    {
        if (is_string($node)) {
            $node = new RawNode($node);
        }

        if (!$node instanceof Node) {
            $this->getEnvironment()->getErrorManager('addNode($node): $node should be a string or a Node');
        }

        $this->nodes[] = $node;
    }

    /**
     * @param Node $node
     */
    public function prependNode(Node $node): void
    {
        array_unshift($this->nodes, $node);
    }

    /**
     * @param Node $node
     */
    public function addHeaderNode(Node $node): void
    {
        $this->headerNodes[] = $node;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
