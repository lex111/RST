<?php

declare(strict_types=1);

namespace Gregwar\RST\References;

use Gregwar\RST\Reference;
use Gregwar\RST\Environment;

class Doc extends Reference
{
    /** @var string */
    protected $name;

    public function __construct($name = 'doc')
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function resolve(Environment $environment, string $data): array
    {
        $metas = $environment->getMetas();
        $file = $environment->canonicalUrl($data);

        if ($metas) {
            $entry = $metas->get($file);
            $entry['url'] = $environment->relativeUrl('/'.$entry['url']);
        } else {
            $entry = [
                'title' => '(unresolved)',
                'url' => '#'
            ];
        }

        return $entry;
    }

    public function found(Environment $environment, string $data): void
    {
        $environment->addDependency($data);
    }
}
