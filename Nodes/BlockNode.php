<?php

declare(strict_types=1);

namespace Gregwar\RST\Nodes;

abstract class BlockNode extends Node
{
    /**
     * BlockNode constructor.
     *
     * @param array $lines
     */
    public function __construct(array $lines)
    {
        if (count($lines)) {
            $firstLine = $lines[0];
            for ($k = 0; $k < strlen($firstLine); $k++) {
                if (trim($firstLine[$k])) {
                    break;
                }
            }

            foreach ($lines as &$line) {
                $line = substr($line, $k);
            }
        }

        $this->value = implode("\n", $lines);
    }
}
