<?php

declare(strict_types=1);

namespace Gregwar\RST\HTML\Nodes;

use Gregwar\RST\Nodes\TocNode as Base;

class TocNode extends Base
{
    /**
     * @param string $url
     * @param array $titles
     * @param int $level
     * @param array $path
     *
     * @return string
     * @throws \Exception
     */
    protected function renderLevel(string $url, array $titles, int $level = 1, array $path = []): ?string
    {
        if ($level > $this->depth) {
            return null;
        }

        $html = '';
        foreach ($titles as $k => $entry) {
            $path[$level-1] = $k+1;
            [$title, $childs] = $entry;
            $token = 'title.'.implode('.', $path);
            $target = $url.'#'.$token;

            if (is_array($title)) {
                [$title, $target] = $title;
                $info = $this->environment->resolve('doc', $target);
                $target = $this->environment->relativeUrl($info['url']);
            }

            $html .= '<li><a href="'.$target.'">'.$title.'</a></li>';

            if ($childs) {
                $html .= '<ul>';
                $html .= $this->renderLevel($url, $childs, $level+1, $path);
                $html .= '</ul>';
            }
        }

        return $html;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function render(): string
    {
        if (isset($this->options['hidden'])) {
            return '';
        }

        $this->depth = isset($this->options['depth']) ? $this->options['depth'] : 2;

        $html = '<div class="toc"><ul>';
        foreach ($this->files as $file) {
            $reference = $this->environment->resolve('doc', $file);
            $reference['url'] = $this->environment->relativeUrl($reference['url']);
            $html .= $this->renderLevel($reference['url'], $reference['titles']);
        }
        $html .= '</ul></div>';

        return $html;
    }
}
