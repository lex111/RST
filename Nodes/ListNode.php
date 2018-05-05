<?php

declare(strict_types=1);

namespace Gregwar\RST\Nodes;

abstract class ListNode extends Node
{
    /** @var array */
    protected $lines = array();

    /**
     * Infos contains:
     *
     * - text: the line text
     * - depth: the depth in the list level
     * - prefix: the prefix char (*, - etc.)
     * - ordered: true of false if the list is ordered
     *
     * @param array $line
     */
    public function addLine(array $line)
    {
        $this->lines[] = $line;
    }

    public function render(): string
    {
        $depth = -1;
        $value = '';
        $stack = array();

        foreach ($this->lines as $line) {
            $prefix = $line['prefix'];
            $text = $line['text'];
            $ordered = $line['ordered'];
            $newDepth = $line['depth'];

            if ($depth < $newDepth) {
                $tags = $this->createList($ordered);
                $value .= $tags[0];
                $stack[] = array($newDepth, $tags[1]."\n");
                $depth = $newDepth;
            }

            while ($depth > $newDepth) {
                $top = $stack[count($stack)-1];

                if ($top[0] > $newDepth) {
                    $value .= $top[1];
                    array_pop($stack);
                    $top = $stack[count($stack)-1];
                    $depth = $top[0];
                }
            }

            $value .= $this->createElement((string) $text, $prefix)."\n";
        }

        while ($stack) {
            list($d, $closing) = array_pop($stack);
            $value .= $closing; 
        }

        return $value;
    }

    /**
     * @param string $text
     * @param string $prefix
     *
     * @return string
     */
    abstract protected function createElement(string $text, string $prefix): string;

    /**
     * @param bool $ordered
     *
     * @return string
     */
    abstract protected function createList(bool $ordered): array;
}
