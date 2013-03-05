<?php
/**
 * Description of Instance
 * @see http://knws.ru/docs/Instance Documentation of Knws\Instance.
 * @author Barif
 */

namespace Knws;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\ArrayLoader;

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
        self::initTranslation('ru_RU');
        self::initTemplate();
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

    /**
     * initTranslation description
     * @see http://knws.ru/docs/Instance/initTranslation Documentation of Knws\Instance->initTranslation().
     * @param string $lang
     * @return void
     */
    public static function initTranslation($lang)
    {
        /*$translator = new Translator('fr_FR', new MessageSelector());
        $translator->setFallbackLocale('fr');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', array(
            'Hello World!' => 'Bonjour',
        ), 'fr');

        echo $translator->trans('Hello World!') . "\n";*/
    }

    /**
     * initTemplate description
     * @see http://knws.ru/docs/Instance/initTemplate Documentation of Knws\Instance->initTemplate().
     * @return void
     */
    public static function initTemplate()
    {
        \Knws\Service\Template::init();
    }

    /**
     * run description
     * @see http://knws.ru/docs/Instance/run Documentation of Knws\Instance->run().
     */
    public static function run()
    {
        echo \Knws\Service\Template::render('body.twig', array());
    }



}
