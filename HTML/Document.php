<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML;

use Gregwar\RST\Document as Base;

use Gregwar\RST\Nodes\RawNode;

class Document extends Base
{
    /**
     * @return string
     */
    public function render(): string
    {
        $document = '';
        foreach ($this->nodes as $node) {
            $document .= $node->render() . "\n";
        }

        return $document;
    }

    /**
     * @return string
     */
    public function renderDocument(): string
    {
        $document = "<!DOCTYPE html>\n";
        $document .= "<html>\n";

        $document .= "<head>\n";
        $document .= "<meta charset=\"utf-8\" />\n";
        foreach ($this->headerNodes as $node) {
            $document .= $node->render()."\n";
        }
        $document .= "</head>\n";

        $document .= "<body>\n";
        $document .= $this->render();
        $document .= "</body>\n";
        $document .= "</html>\n";

        return $document;
    }

    /**
     * @param string $css
     */
    public function addCss(string $css): void
    {
        $environment = $this->getEnvironment();
        $css = $environment->relativeUrl($css);

        $this->addHeaderNode(new RawNode('<link rel="stylesheet" type="text/css" href="'.htmlspecialchars($css).'" />'));
    }

    /**
     * @param string $js
     */
    public function addJs(string $js): void
    {
        $environment = $this->getEnvironment();
        $js = $environment->relativeUrl($js);

        $this->addHeaderNode(new RawNode('<script type="text/javascript" src="'.htmlspecialchars($js).'"></script>'));
    }

    /**
     * @param string $url
     */
    public function addFavicon($url = '/favicon.ico'): void
    {
        $environment = $this->getEnvironment();
        $url = $environment->relativeUrl($url);

        $this->addHeaderNode(new RawNode('<link rel="icon" type="image/x-icon" href="'.htmlspecialchars($url).'" />'));
    }
}
