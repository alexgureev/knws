<?php
/**
 * Description of Instance
 * @see http://knws.ru/docs/Instance Documentation of Knws\Instance.
 * @author Barif
 */

namespace Knws;

class Instance
{
    public static $config = array();

    /**
     * init description
     * @see http://knws.ru/docs/Instance/init Documentation of Knws\Instance->init.
     * @return void
     */
    public static function init()
    {
        \Knws\Service\Config::loadConfig();
        self::initLogger();
    }

    /**
     * initLogger description
     * @see http://knws.ru/docs/Instance/initLogger Documentation of Knws\Instance->initLogger().
     * @return array $result
     */
    public static function initLogger()
    {
        $logger = new \Monolog\Logger("Mail");
        $logger->pushHandler(new \Monolog\Handler\RotatingFileHandler($_SERVER['DOCUMENT_ROOT'] . "/../data/logs/monolog.log"));
        try {
            \Knws\Logger::init($logger, 100);
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
}
