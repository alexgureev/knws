<?php
/**
 * PSR-3 Logger adapter
 */
namespace Knws;

use Psr\Log\LoggerInterface;

class Logger
{
    /** @param \Psr\Log\LoggerInterface $logger logger static instance */
    protected static $logger = null;
    /** @param int $logLevel logging level */
    protected static $logLevel = 300;
    /** @param array $logLevels defined logging levels */
    protected static $logLevels = array('debug' => 100, 'info' => 200, 'notice' => 250, 'warning' => 300, 'error' => 400, 'critical' => 500, 'alert' => 550, 'emergency' => 600);

    /**
     * Logger initialize
     * @see http://knws.ru/docs/Logger/init Documentation of Knws\Logger->init().
     * @param \Psr\Log\LoggerInterface $logger
     * @param int $logLevel
     * @return void
     * @throws \Exception
     */
    public static function init(LoggerInterface $logger = null, int $logLevel = 300)
    {
        self::$logger = $logger;
        self::$logLevel = $logLevel;

        try {
            self::info('Logger initialized');
        } catch (\Exception $e) {
            throw new \Exception('shit happend');
        }
    }

    /**
     * emergency log event
     * @see http://knws.ru/docs/Logger/emergency Documentation of Knws\Logger->emergency().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function emergency($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['emergency']) {
            self::$logger->emergency($message, $context);
        }
    }

    /**
     * alert log event
     * @see http://knws.ru/docs/Logger/alert Documentation of Knws\Logger->alert().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function alert($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['alert']) {
            self::$logger->alert($message, $context);
        }
    }

    /**
     * critical log event
     * @see http://knws.ru/docs/Logger/critical Documentation of Knws\Logger->critical().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function critical($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['critical']) {
            self::$logger->critical($message, $context);
        }
    }

    /**
     * error log event
     * @see http://knws.ru/docs/Logger/error Documentation of Knws\Logger->error().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function error($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['error']) {
            self::$logger->error($message, $context);
        }
    }

    /**
     * warning log event
     * @see http://knws.ru/docs/Logger/warning Documentation of Knws\Logger->warning().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function warning($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['warning']) {
            self::$logger->warning($message, $context);
        }
    }

    /**
     * notice log event
     * @see http://knws.ru/docs/Logger/notice Documentation of Knws\Logger->notice().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function notice($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['notice']) {
            self::$logger->notice($message, $context);
        }
    }

    /**
     * info log event
     * @see http://knws.ru/docs/Logger/info Documentation of Knws\Logger->info().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function info($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['info']) {
            self::$logger->info($message, $context);
        }
    }

    /**
     * debug log event
     * @see http://knws.ru/docs/Logger/debug Documentation of Knws\Logger->debug().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function debug($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['debug']) {
            self::$logger->debug($message, $context);
        }
    }
}
