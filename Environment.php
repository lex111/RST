<?php

declare(strict_types=1);

namespace Gregwar\RST;

use Gregwar\RST\Nodes\Node;

class Environment
{
    /** @var array Letters used as separators for titles and horizontal line */
    public static $letters = ['=', '-', '~', '*', '^', '"'];

    /** @var ErrorManager|null */
    public $errorManager = null;

    // Table letters
    /** @var string */
    public static $tableLetter = '=';
    /** @var string */
    public static $prettyTableLetter = '-';
    /** @var string */
    public static $prettyTableHeader = '=';
    /** @var string */
    public static $prettyTableJoint = '+';

    // Title letter for each levels
    /** @var int */
    protected $currentTitleLevel = 0;
    /** @var array */
    protected $titleLetters = [];

    // Current file name
    /** @var string */
    protected $currentFileName = '';
    /** @var string */
    protected $currentDirectory = '.';
    /** @var string */
    protected $targetDirectory = '.';
    /** @var string|null */
    protected $url = null;

    /** @var array References that can be resolved */
    protected $references = [];

    /** @var Metas|null */
    protected $metas = null;

    /** @var array Dependencies of this document */
    protected $dependencies = [];

    /** @var array Variables of the document */
    protected $variables = [];

    /** @var array Links */
    protected $links = [];

    /** @var array Level counters */
    protected $levels = [];
    /** @var array */
    protected $counters = [];

    /** @var bool Enable relative URLs */
    protected $relativeUrls = true;

    /** @var array Anonymous links stack */
    protected $anonymous = [];

    /**
     * Environment constructor.
     */
    public function __construct()
    {
        $this->errorManager = new ErrorManager;

        $this->reset();
    }

    /**
     * Puts the environment in a clean state for a new parse, like title level order.
     */
    public function reset(): void
    {
        $this->titleLetters = [];
        $this->currentTitleLevel = 0;
        $this->levels = [];
        $this->counters = [];

        for ($level = 0; $level < 16; $level++) {
            $this->levels[$level] = 1;
            $this->counters[$level] = 0;
        }
    }

    public function getErrorManager(): ErrorManager
    {
        return $this->errorManager;
    }

    public function setErrorManager(ErrorManager $errorManager)
    {
        $this->errorManager = $errorManager;
    }

    public function setMetas(Metas $metas): void
    {
        $this->metas = $metas;
    }

    /**
     * Get my parent metas
     */
    public function getParent(): ?array
    {
        if (!$this->currentFileName || !$this->metas) {
            return null;
        }

        $meta = $this->metas->get($this->currentFileName);

        if (!$meta || !isset($meta['parent'])) {
            return null;
        }

        $parent = $this->metas->get($meta['parent']);

        if (!$parent) {
            return null;
        }

        return $parent;
    }

    /**
     * Get the docs involving this document
     */
    public function getMyToc(): ?array
    {
        $parent = $this->getParent();

        if ($parent) {
            foreach ($parent['tocs'] as $toc) {
                if (in_array($this->currentFileName, $toc)) {
                    $before = [];
                    $after = $toc;

                    while ($after) {
                        $file = array_shift($after);
                        if ($file == $this->currentFileName) {
                            return [$before, $after];
                        }
                        $before[] = $file;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Registers a new reference
     *
     * @param Reference $reference
     */
    public function registerReference(Reference $reference): void
    {
        $name = $reference->getName();
        $this->references[$name] = $reference;
    }

    /**
     * Resolves a reference
     *
     * @param string $section
     * @param string $data
     *
     * @return mixed
     * @throws \Exception
     */
    public function resolve(string $section, string $data)
    {
        if (isset($this->references[$section])) {
            $reference = $this->references[$section];

            return $reference->resolve($this, $data);
        }

        $this->errorManager->error('Unknown reference section '.$section);
    }

    /**
     * @param string $section
     * @param string $data
     *
     * @return mixed
     * @throws \Exception
     */
    public function found(string $section, string $data): void
    {
        if (isset($this->references[$section])) {
            /** @var Reference $reference */
            $reference = $this->references[$section];

            $reference->found($this, $data);
            return;
        }

        $this->errorManager->error('Unknown reference section '.$section);
    }

    /**
     * Sets the giving variable to a value
     *
     * @param string$variable the variable name
     * @param Node $value the variable value
     */
    public function setVariable(string $variable, Node $value)
    {
        $this->variables[$variable] = $value;
    }

    /**
     * Title level
     *
     * @param int $level
     *
     * @return string
     */
    public function createTitle(int $level)
    {
        for ($currentLevel = 0; $currentLevel < 16; $currentLevel++) {
            if ($currentLevel > $level) {
                $this->levels[$currentLevel] = 1;
                $this->counters[$currentLevel] = 0;
            }
        }

        $this->levels[$level] = 1;
        $this->counters[$level]++;
        $token = ['title'];

        for ($i = 1; $i <= $level; $i++) {
            $token[] = $this->counters[$i];
        }

        return implode('.', $token);
    }

    /**
     * Get a level number
     *
     * @param int $level
     *
     * @return int
     */
    public function getNumber(int $level)
    {
        return $this->levels[$level]++;
    }

    /**
     * Gets the variable value
     *
     * @param string $variable
     * @param null $default
     *
     * @return mixed|null
     */
    public function getVariable(string $variable, $default = null)
    {
        if (isset($this->variables[$variable])) {
            return $this->variables[$variable];
        }

        return $default;
    }

    /**
     * Set the link url
     *
     * @param string $name
     * @param string $url
     */
    public function setLink(string $name, string $url): void
    {
        $name = trim(strtolower($name));

        if ($name == '_') {
            $name = array_shift($this->anonymous);
        }

        $this->links[$name] = trim($url);
    }

    /**
     * Resets the anonymous stack
     */
    public function resetAnonymousStack(): void
    {
        $this->anonymous = [];
    }

    /**
     * Set the current anonymous links name
     *
     * @param string $name
     */
    public function pushAnonymous(string $name): void
    {
        $this->anonymous[] = trim(strtolower($name));
    }

    /**
     * Get a link value
     *
     * @param string $name
     * @param bool $relative
     *
     * @return string|null
     */
    public function getLink(string $name, bool $relative = true)
    {
        $name = trim(strtolower($name));
        if (isset($this->links[$name])) {
            $link = $this->links[$name];

            if ($relative) {
                return $this->relativeUrl($link);
            }

            return $link;
        }

        return null;
    }

    /**
     * Adds a dependency to the document
     *
     * @param string $dependency
     */
    public function addDependency(string $dependency)
    {
        $dependency = $this->canonicalUrl($dependency);
        $this->dependencies[] = $dependency;
    }

    /**
     * Getting all the dependencies for this environment
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * Resolves a relative URL using directories, for instance, if the
     * current directory is "path/to/something", and you want to get the
     * relative URL to "path/to/something/else.html", the result will
     * be else.html. Else, "../" will be added to go to the upper directory
     *
     * @param string $url
     *
     * @return string
     */
    public function relativeUrl(string $url): string
    {
        // If string contains ://, it is considered as absolute
        if (preg_match('/:\\/\\//mUsi', $url)) {
            return $url;
        }

        // If string begins with "/", the "/" is removed to resolve the
        // relative path
        if (strlen($url) && $url[0] == '/') {
            $url = substr($url, 1);
            if ($this->samePrefix($url)) {
                // If the prefix is the same, simply returns the file name
                $relative = basename($url);
            } else {
                // Else, returns enough ../ to get upper
                $relative = '';

                for ($k = 0; $k < $this->getDepth(); $k++) {
                    $relative .= '../';
                }

                $relative .= $url;
            }
        } else {
            $relative = $url;
        }

        return $relative;
    }

    /**
     * Use relative URLs for links
     */
    public function useRelativeUrls(): bool
    {
        return $this->relativeUrls;
    }

    /**
     * Use relative URLs for links
     *
     * @param bool $enable
     */
    public function setUseRelativeUrls(bool $enable): void
    {
        $this->relativeUrls = $enable;
    }

    /**
     * Get the depth of the current file name (the number of parent
     * directories)
     */
    public function getDepth(): int
    {
        return count(explode('/', $this->currentFileName))-1;
    }

    /**
     * Returns true if the given url have the same prefix as the
     * current document
     *
     * @param string $url
     *
     * @return bool
     */
    protected function samePrefix(string $url)
    {
        $partsA = explode('/', $url);
        $partsB = explode('/', $this->currentFileName);

        $n = count($partsA);
        if ($n != count($partsB)) {
            return false;
        }

        unset($partsA[$n-1]);
        unset($partsB[$n-1]);

        return $partsA == $partsB;
    }

    /**
     * Returns the directory name
     */
    public function getDirName(): string
    {
        $dirname = dirname($this->currentFileName);

        if ($dirname == '.') {
            return '';
        }

        return $dirname;
    }

    /**
     * Canonicalize a path, a/b/c/../d/e will become
     * a/b/d/e
     *
     * @param string $url
     *
     * @return string
     */
    protected function canonicalize(string $url): string
    {
        $parts = explode('/', $url);
        $stack = [];

        foreach ($parts as $part) {
            if ($part == '..') {
                array_pop($stack);
            } else {
                $stack[] = $part;
            }
        }

        return implode('/', $stack);
    }

    /**
     * Gets a canonical URL from the given one
     *
     * @param string $url
     *
     * @return string
     */
    public function canonicalUrl(string $url): ?string
    {
        if (strlen($url)) {
            if ($url[0] == '/') {
                // If the URL begins with a "/", the following is the
                // canonical URL
                return substr($url, 1);
            } else {
                // Else, the canonical name is under the current dir
                if ($this->getDirName()) {
                    return $this->canonicalize($this->getDirName() . '/' .$url);
                } else {
                    return $this->canonicalize($url);
                }
            }
        }

        return null;
    }

    /**
     * Sets the current file name
     *
     * @param string $filename
     */
    public function setCurrentFileName(string $filename): void
    {
        $this->currentFileName = $filename;
    }

    /**
     * Sets the directory of the current parsing
     *
     * @param string $directory
     */
    public function setCurrentDirectory(string $directory): void
    {
        $this->currentDirectory = $directory;
    }

    /**
     * Returns an absolute path for a relative given URL
     *
     * @param string $url
     *
     * @return string
     */
    public function absoluteRelativePath(string $url): string
    {
        return $this->currentDirectory . '/' . $this->getDirName() . '/' . $this->relativeUrl($url);
    }

    /**
     * @param string $directory
     */
    public function setTargetDirectory(string $directory): void
    {
        $this->targetDirectory = $directory;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function getUrl(): string
    {
        if ($this->url) {
            return $this->url;
        } else {
            return $this->currentFileName;
        }
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        if ($this->getDirName()) {
            $url = $this->getDirName() . '/' . $url;
        }

        $this->url = $url;
    }

    public function getMetas(): ?Metas
    {
        return $this->metas;
    }

    public function getLevel($letter): int
    {
        foreach ($this->titleLetters as $level => $titleLetter) {
            if ($letter == $titleLetter) {
                return $level;
            }
        }

        $this->currentTitleLevel++;
        $this->titleLetters[$this->currentTitleLevel] = $letter;

        return $this->currentTitleLevel;
    }

    public function getTitleLetters(): array
    {
        return $this->titleLetters;
    }
}
