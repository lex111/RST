<?php

declare(strict_types=1);

namespace Gregwar\RST;

/**
 * A builder can parses a whole directory to build the target architecture
 * of a document
 */
class Builder
{
    const NO_PARSE = 1;
    const PARSE = 2;

    /** @var string Tree index name */
    protected $indexName = 'index';

    /** @var ErrorManager|null Error manager */
    protected $errorManager = null;

    /** @var bool Verbose build ? */
    protected $verbose = true;

    /** @var array Files to copy at the end of the build */
    protected $toCopy = [];
    /** @var array */
    protected $toMkdir = [];

    /** @var string */
    protected $directory;
    /** @var string */
    protected $targetDirectory;

    /** @var Metas */
    protected $metas;

    /** @var array States (decision) of the scanned documents */
    protected $states = [];

    /** @var array Queue of documents to be parsed */
    protected $parseQueue = [];

    /** @var array Parsed documents waiting to be rendered */
    protected $documents = [];

    /** @var Kernel */
    protected $kernel;

    /** @var array Hooks before the parsing on the environment */
    protected $beforeHooks = [];

    /** @var array Hooks after the parsing */
    protected $hooks = [];

    /** @var bool Use relative URLs */
    protected $relativeUrls = true;

    /**
     * Builder constructor.
     *
     * @param Kernel|null $kernel
     */
    public function __construct($kernel = null)
    {
        $this->errorManager = new ErrorManager;
        $this->kernel = $kernel ?: new HTML\Kernel;

        $this->kernel->initBuilder($this);
    }

    /**
     * @return ErrorManager
     */
    public function getErrorManager(): ErrorManager
    {
        return $this->errorManager;
    }

    /**
     * Adds an hook which will be called on each document after parsing
     *
     * @param callable $function
     *
     * @return Builder
     */
    public function addHook(callable $function): self
    {
        $this->hooks[] = $function;

        return $this;
    }

    /**
     * Adds an hook which will be called on each environment during building
     *
     * @param callable $function
     *
     * @return Builder
     */
    public function addBeforeHook(callable $function): self
    {
        $this->beforeHooks[] = $function;

        return $this;
    }

    /**
     * @param string $text
     */
    protected function display(string $text): void
    {
        if ($this->verbose) {
            echo $text."\n";
        }
    }

    /**
     * @param string $directory
     * @param string $targetDirectory
     * @param bool $verbose
     *
     * @throws \Exception
     */
    public function build(string $directory, string $targetDirectory = 'output', bool $verbose = true): void
    {
        $this->verbose = $verbose;
        $this->directory = $directory;
        $this->targetDirectory = $targetDirectory;

        // Creating output directory if doesn't exists
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        // Try to load metas, if it does not exists, create it
        $this->display('* Loading metas');
        $this->metas = new Metas($this->loadMetas());

        // Scan all the metas and the index
        $this->display('* Pre-scanning files');
        $this->scan($this->getIndexName());
        $this->scanMetas();

        // Parses all the documents
        $this->parseAll();

        // Renders all the documents
        $this->render();

        // Saving the meta
        $this->display('* Writing metas');
        $this->saveMetas();

        // Copy the files
        $this->display('* Running the copies');
        $this->doMkdir();
        $this->doCopy();
    }

    /**
     * Renders all the pending documents
     */
    protected function render(): void
    {
        $this->display('* Rendering documents');
        foreach ($this->documents as $file => &$document) {
            $this->display(' -> Rendering '.$file.'...');
            $target = $this->getTargetOf($file);

            $directory = dirname($target);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($target, $document->renderDocument());
        }
    }

    /**
     * Adding a file to the parse queue
     *
     * @param string $file
     */
    protected function addToParseQueue(string $file)
    {
        $this->states[$file] = self::PARSE;

        if (!isset($this->documents[$file])) {
            $this->parseQueue[$file] = $file;
        }
    }

    /**
     * Returns the next file to parse
     */
    protected function getFileToParse(): ?string
    {
        if ($this->parseQueue) {
            return array_shift($this->parseQueue);
        } else {
            return null;
        }
    }

    /**
     * Parses all the document that need to be parsed
     *
     * @throws \Exception
     */
    protected function parseAll(): void
    {
        $this->display('* Parsing files');
        while ($file = $this->getFileToParse()) {
            $this->display(' -> Parsing '.$file.'...');
            // Process the file
            $rst = $this->getRST($file);
            $parser = new Parser(null, $this->kernel);

            $environment = $parser->getEnvironment();
            $environment->setMetas($this->metas);
            $environment->setCurrentFilename($file);
            $environment->setCurrentDirectory($this->directory);
            $environment->setTargetDirectory($this->targetDirectory);
            $environment->setErrorManager($this->errorManager);
            $environment->setUseRelativeUrls($this->relativeUrls);

            foreach ($this->beforeHooks as $hook) {
                $hook($parser);
            }

            if (!file_exists($rst)) {
                $this->errorManager->error('Can\'t parse the file '.$rst);
                continue;
            }

            $document = $this->documents[$file] = $parser->parseFile($rst);

            // Calling all the post-process hooks
            foreach ($this->hooks as $hook) {
                $hook($document);
            }

            // Calling the kernel document tweaking
            $this->kernel->postParse($document);

            $dependencies = $document->getEnvironment()->getDependencies();

            if ($dependencies) {
                $this->display(' -> Scanning dependencies of '.$file.'...');
                // Scan the dependencies for this document
                foreach ($dependencies as $dependency) {
                    $this->scan($dependency);
                }
            }

            // Append the meta for this document
            $this->metas->set(
                $file,
                $this->getUrl($document),
                $document->getTitle(),
                $document->getTitles(),
                $document->getTocs(),
                filectime($rst),
                $dependencies
            );
        }
    }

    /**
     * Scans a file, this will check the status of the file and tell if it
     * needs to be parsed or not
     *
     * @param string $file
     */
    public function scan(string $file)
    {
        // If no decision is already made about this file
        if (!isset($this->states[$file])) {
            $this->display(' -> Scanning '.$file.'...');
            $this->states[$file] = self::NO_PARSE;
            $entry = $this->metas->get($file);
            $rst = $this->getRST($file);

            if (!$entry || !file_exists($rst) || $entry['ctime'] < filectime($rst)) {
                // File was never seen or changed and thus need to be parsed
                $this->addToParseQueue($file);
            } else {
                // Have a look to the file dependencies to knoww if you need to parse
                // it or not
                $depends = $entry['depends'];

                if (isset($entry['parent'])) {
                    $depends[] = $entry['parent'];
                }

                foreach ($depends as $dependency) {
                    $this->scan($dependency);

                    // If any dependency needs to be parsed, this file needs also to be
                    // parsed
                    if ($this->states[$dependency] == self::PARSE) {
                        $this->addToParseQueue($file);
                    }
                }
            }
        }
    }

    /**
     * Scans all the metas
     */
    public function scanMetas(): void
    {
        $entries = $this->metas->getAll();

        foreach ($entries as $file => $infos) {
            $this->scan($file);
        }
    }

    /**
     * Get the meta file name
     */
    protected function getMetaFile(): string
    {
        return $this->getTargetFile('meta.php');
    }


    /**
     * Try to inport the metas from the meta files
     */
    protected function loadMetas(): ?array
    {
        $metaFile = $this->getMetaFile();

        if (file_exists($metaFile)) {
            return @include($metaFile);
        }

        return null;
    }

    /**
     * Saving the meta files
     */
    protected function saveMetas(): void
    {
        $metas = '<?php return '.var_export($this->metas->getAll(), true).';';
        file_put_contents($this->getMetaFile(), $metas);
    }

    /**
     * Gets the .rst of a source file
     *
     * @param string $file
     *
     * @return string
     */
    public function getRST(string $file): string
    {
        return $this->getSourceFile($file . '.rst');
    }

    /**
     * Gets the name of a target for a file, for instance /introduction/part1 could
     * be resolved into /path/to/introduction/part1.html
     *
     * @param string $file
     *
     * @return string
     */
    public function getTargetOf(string $file): string
    {
        $meta = $this->metas->get($file);

        return $this->getTargetFile($meta['url']);
    }

    /**
     * Gets the URL of a target file
     *
     * @param Document $document
     *
     * @return string
     */
    public function getUrl(Document $document): string
    {
        $environment = $document->getEnvironment();

        return $environment->getUrl() . '.' . $this->kernel->getFileExtension();
    }

    /**
     * Gets the name of a target file
     *
     * @param string $filename
     *
     * @return string
     */
    public function getTargetFile(string $filename): string
    {
        return $this->targetDirectory . '/' . $filename;
    }

    /**
     * Gets the name of a source file
     *
     * @param string $filename
     *
     * @return string
     */
    public function getSourceFile(string $filename): string
    {
        return $this->directory . '/' . $filename;
    }

    /**
     * Run the copy
     */
    public function doCopy(): void
    {
        foreach ($this->toCopy as $copy) {
            list($source, $destination) = $copy;
            if ($source[0] != '/') {
                $source = $this->getSourceFile($source);
            }
            $destination = $this->getTargetFile($destination);

            if (is_dir($source) && is_dir($destination)) {
                $destination = dirname($destination);
            }

            shell_exec('cp -R '.$source.' '.$destination);
        }
    }

    /**
     * Add a file to copy
     *
     * @param string $source
     * @param string|null $destination
     *
     * @return Builder
     */
    public function copy(string $source, ?string $destination = null)
    {
        if ($destination === null) {
            $destination = basename($source);
        }

        $this->toCopy[] = [$source, $destination];

        return $this;
    }

    /**
     * Run the directories creation
     */
    public function doMkdir(): void
    {
        foreach ($this->toMkdir as $mkdir) {
            $dir = $this->getTargetFile($mkdir);

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * Creates a directory in the target
     *
     * @param string $directory the directory name to create
     *
     * @return Builder
     */
    public function mkdir(string $directory): self
    {
        $this->toMkdir[] = $directory;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return Builder
     */
    public function setIndexName(string $name): self
    {
        $this->indexName = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getIndexName(): string
    {
        return $this->indexName;
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
}
