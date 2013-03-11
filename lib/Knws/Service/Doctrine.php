<?php
/**
 * Description of Doctrine
 * @see http://knws.ru/docs/Service/Doctrine Documentation of Knws/Service\Doctrine.
 * @author Barif
 */

namespace Knws\Service;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Doctrine extends \Knws\Service
{
    public static $instance;
    protected static $class;

    /**
     * Initialize EntityManager
     * return void
     */
    public static function init()
    {
        $isDevMode = true;
        //$config = Setup::createAnnotationMetadataConfiguration(array(APPPATH . "/entities"), $isDevMode);
        $config = Setup::createYAMLMetadataConfiguration(array(APPPATH . "/configs/"), $isDevMode);
        self::$instance = EntityManager::create(\Knws\Instance::$config['config']['Doctrine'], $config);
    }
}
