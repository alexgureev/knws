<?php
/**
 * PSR-3 Logger adapter
 */
namespace Knws\Service;

class Logger extends \Knws\Service
{
    protected static $class;

    /** @param obj $instance logger static instance */
    protected static $instance;
    /** @param int $logLevel logging level */
    protected static $logLevel = 300;
    /** @param array $logLevels defined logging levels */
    protected static $logLevels = array('debug' => 100, 'info' => 200, 'notice' => 250, 'warning' => 300, 'error' => 400, 'critical' => 500, 'alert' => 550, 'emergency' => 600);

    /**
     * Logger initialize
     * @see http://knws.ru/docs/Service/Logger/init Documentation of Knws\Logger->init().
     * @param \Psr\Log\LoggerInterface $logger
     * @param int $logLevel
     * @return void
     * @throws \Exception
     * @todo move monolog initialize to module
     */
    public static function init()
    {
        self::$class = self::getClassName(__CLASS__);
        //$engine = '\Knws\Module\\' . \Knws\Instance::$config['config'][self::$class]['engine'];
        self::$instance = new \Monolog\Logger(\Knws\Instance::$config['config'][self::$class]['channel']);
        self::$instance->pushHandler(new \Monolog\Handler\RotatingFileHandler($_SERVER['DOCUMENT_ROOT'] . "/../data/logs/monolog.log"));
    }

    /**
     * emergency log event
     * @see http://knws.ru/docs/Service/Logger/emergency Documentation of Knws\Logger->emergency().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function emergency($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['emergency']) {
            self::$instance->emergency($message, $context);
        }
    }

    /**
     * alert log event
     * @see http://knws.ru/docs/Service/Logger/alert Documentation of Knws\Logger->alert().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function alert($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['alert']) {
            self::$instance->alert($message, $context);
        }
    }

    /**
     * critical log event
     * @see http://knws.ru/docs/Service/Logger/critical Documentation of Knws\Logger->critical().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function critical($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['critical']) {
            self::$instance->critical($message, $context);
        }
    }

    /**
     * error log event
     * @see http://knws.ru/docs/Service/Logger/error Documentation of Knws\Logger->error().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function error($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['error']) {
            self::$instance->error($message, $context);
        }
    }

    /**
     * warning log event
     * @see http://knws.ru/docs/Service/Logger/warning Documentation of Knws\Logger->warning().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function warning($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['warning']) {
            self::$instance->warning($message, $context);
        }
    }

    /**
     * notice log event
     * @see http://knws.ru/docs/Service/Logger/notice Documentation of Knws\Logger->notice().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function notice($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['notice']) {
            self::$instance->notice($message, $context);
        }
    }

    /**
     * info log event
     * @see http://knws.ru/docs/Service/Logger/info Documentation of Knws\Logger->info().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function info($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['info']) {
            self::$instance->info($message, $context);
        }
    }

    /**
     * debug log event
     * @see http://knws.ru/docs/Service/Logger/debug Documentation of Knws\Logger->debug().
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function debug($message, $context = array())
    {
        if(self::$logLevel<=self::$logLevels['debug']) {
            self::$instance->debug($message, $context);
        }
    }

    /**
     * setLogLevel description
     * @see http://knws.ru/docs/Service/setLogLevel Documentation of Knws\Service->setLogLevel().
     * @param int $level
     * @return void
     */
    public static function setLogLevel($level)
    {
        self::$logLevel = $level;
    }

}
