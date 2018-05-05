<?php

declare(strict_types=1);

namespace Gregwar\RST\Nodes;

use Gregwar\RST\Parser;

abstract class TableNode extends Node
{
    /** @var array */
    protected $parts;
    /** @var array */
    protected $data = [];
    /** @var array */
    protected $headers = [];

    /**
     * TableNode constructor.
     *
     * @param array $parts
     */
    public function __construct(array $parts)
    {
        $this->parts = $parts;
        $this->data[] = [];
    }

    /**
     * Gets the columns count of the table
     */
    public function getCols()
    {
        return count($this->parts[2]);
    }

    /**
     * Gets the rows count of the table
     */
    public function getRows(): int
    {
        return count($this->data)-1;
    }

    /**
     * @param array|bool $parts
     * @param string $line
     *
     * @return bool
     */
    public function push($parts, ?string $line): bool
    {
        $line = utf8_decode($line);

        if ($parts) {
            // New line in the tab
            if ($parts[2] != $this->parts[2]) {
                return false;
            }

            if ($parts[0]) {
                $this->headers[count($this->data)-1] = true;
            }
            $this->data[] = [];
        } else {
            // Pushing data in the cells
            [, $pretty, $parts] = $this->parts;
            $row = &$this->data[count($this->data)-1];

            for ($k = 1; $k <= count($parts); $k++) {
                if ($k == count($parts)) {
                    $data = substr($line, $parts[$k-1]);
                } else {
                    $data = substr($line, $parts[$k-1], $parts[$k]-$parts[$k-1]);
                }

                if ($pretty) {
                    $data = substr($data, 0, -1);
                }

                $data = utf8_encode(trim($data));

                if (isset($row[$k-1])) {
                    $row[$k-1] .= ' '.$data;
                } else {
                    $row[$k-1] = $data;
                }
            }
        }

        return true;
    }

    public function finalize(Parser $parser): void
    {
        foreach ($this->data as &$row) {
            if ($row) {
                foreach ($row as &$col) {
                    $col = $parser->createSpan($col);
                }
            }
        }
    }
}
