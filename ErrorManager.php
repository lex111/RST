<?php

declare(strict_types=1);

namespace Gregwar\RST;

class ErrorManager
{
    /** @var bool */
    protected $abort = true;

    /**
     * @param bool $abort
     */
    public function abortOnError(bool $abort)
    {
        $this->abort = $abort;
    }

    /**
     * @param string $message
     *
     * @throws \Exception
     */
    public function error(string $message): void
    {
        if ($this->abort) {
            throw new \Exception($message);
        } else {
            echo '/!\\ '.$message."\n";
        }
    }
}
