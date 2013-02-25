<?php namespace Knws;

use Psr\Log\LoggerInterface;

class Logger
{
    protected static $logger;

    public static function init(LoggerInterface $logger = null)
    {
        self::$logger = $logger;
    }


    public static function doSomething()
    {
        if (self::$logger) {
            self::$logger->info('Doing work');
        }
    }

    /*
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';
     */
}