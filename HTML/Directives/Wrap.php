<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Directives;

use Gregwar\RST\Nodes\Node;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;
use Gregwar\RST\Nodes\WrapperNode;

/**
 * Wraps a sub document in a div with a given class
 */
class Wrap extends SubDirective
{
    /** @var string */
    protected $class;
    /** @var bool */
    protected $uniqid;

    /**
     * Wrap constructor.
     *
     * @param string $class
     * @param bool $uniqid
     */
    public function __construct(string $class, bool $uniqid = false)
    {
        $this->class = $class;
        $this->uniqid = $uniqid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->class;
    }

    /**
     * @param Parser $parser
     * @param Node $document
     * @param string $variable
     * @param string $data
     * @param array $options
     *
     * @return WrapperNode
     */
    public function processSub(Parser $parser, ?Node $document, string $variable, string $data, array $options): Node
    {
        $class = $this->class;
        if ($this->uniqid) {
            $id = ' id="'.uniqid($this->class).'"';
        } else {
            $id = '';
        }

        return new WrapperNode($document, '<div class="'.$class.'"'.$id.'>', '</div>');
    }
}
