<?php namespace Knws;

use Psr\Log\LoggerInterface;

class Logger
{
    private $logger;
    /**
     * @param LoggerInterface $Logger PSR-3 logger interface
     */
    public function __construct(LoggerInterface $logger = null)
    {
        /** @type int This is a counter. */
        $this->logger = $logger;
    }


    public function doSomething()
    {
        if ($this->logger) {
            $this->logger->info('Doing work');
        }

        // do something useful
    }
}