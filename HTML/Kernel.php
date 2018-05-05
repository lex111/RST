<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML;

use Gregwar\RST\Kernel as Base;

class Kernel extends Base
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'HTML';
    }

    /**
     * @return array
     */
    public function getDirectives(): array
    {
        $directives = parent::getDirectives();

        $directives = array_merge($directives, [
            new Directives\Image,
            new Directives\Figure,
            new Directives\Meta,
            new Directives\Stylesheet,
            new Directives\Title,
            new Directives\Url,
            new Directives\Div,
            new Directives\Wrap('note')
        ]);

        return $directives;
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return 'html';
    }
}
