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
        //self::initLogger();
        self::initRequest();
        self::initResponce();
        //self::initTranslation('ru_RU');
        self::initTemplate();
        self::initDB();
        self::initDoctrine();
    }

    /**
     * initLogger description
     * @see http://knws.ru/docs/Instance/initLogger Documentation of Knws\Instance->initLogger().
     * @return array $result
     */
    public static function initLogger()
    {
        \Knws\Service\Logger::init();
    }

    /**
     * initLogger description
     * @see http://knws.ru/docs/Instance/initLogger Documentation of Knws\Instance->initLogger().
     * @return array $result
     */
    public static function initDoctrine()
    {
        \Knws\Service\Doctrine::init();
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
     * initDB description
     * @see http://knws.ru/docs/Instance/initDB Documentation of Knws\Instance->initDB().
     * @return void
     */
    public static function initDB()
    {
        \Knws\Service\DB::init();
    }

    /**
     * initRequest description
     * @see http://knws.ru/docs/Instance/initRequest Documentation of Knws\Instance->initRequest().
     * @return void
     */
    public static function initRequest()
    {
        \Knws\Service\Request::init();
    }

    /**
     * initResponce description
     * @see http://knws.ru/docs/Instance/initResponce Documentation of Knws\Instance->initResponce().
     * @return void
     */
    public static function initResponce()
    {
        \Knws\Service\Response::init();
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
