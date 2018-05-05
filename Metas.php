<?php

declare(strict_types=1);

namespace Gregwar\RST;

class Metas
{
    /** @var array */
    protected $entries = [];
    /** @var array  */
    protected $parents = [];

    /**
     * Metas constructor.
     *
     * @param array $entries
     */
    public function __construct(?array $entries)
    {
        if ($entries) {
            $this->entries = $entries;
        }
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->entries;
    }

    /**
     * Sets the meta for url, giving the title, the modification time and
     * the dependencies list
     *
     * @param string $file
     * @param string $url
     * @param string $title
     * @param array $titles
     * @param array $tocs
     * @param int $ctime
     * @param array $depends
     */
    public function set(string $file, string $url, string $title, array $titles, array $tocs, int $ctime, array $depends): void
    {
        foreach ($tocs as $toc) {
            foreach ($toc as $child) {
                $this->parents[$child] = $file;
                if (isset($this->entries[$child])) {
                    $this->entries[$child]['parent'] = $file;
                }
            }
        }

        $this->entries[$file] = [
            'file' => $file,
            'url' => $url,
            'title' => $title,
            'titles' => $titles,
            'tocs' => $tocs,
            'ctime' => $ctime,
            'depends' => $depends
        ];

        if (isset($this->parents[$file])) {
            $this->entries[$file]['parent'] = $this->parents[$file];
        }
    }

    /**
     * Gets the meta for a given document reference url
     *
     * @param string $url
     *
     * @return array|null
     */
    public function get(string $url): ?array
    {
        return $this->entries[$url] ?? null;
    }
}
