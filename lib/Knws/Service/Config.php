<?php
/**
 * Description of Config
 * @see http://knws.ru/docs/Service/Config Documentation of Knws\Service\Config.
 * @author Barif
 */

namespace Knws\Service;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Exception\DumpException;

class Config
{
    /**
     * loadConfig description
     * @see http://knws.ru/docs/Service/Config/loadConfig Documentation of Knws\Service\Config->loadConfig().
     * @return void
     */
    public static function loadConfig()
    {
        try {
            \Knws\Instance::$config = Yaml::parse($_SERVER['DOCUMENT_ROOT'] . '/../application/configs/application.yml');
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }
    }

    /**
     * saveConfig description
     * @see http://knws.ru/docs/Service/Config/saveConfig Documentation of Knws\Service/Config->saveConfig().
     * @param mixed $config
     * @return bool false if didn't saved
     */
    public static function saveConfig($config)
    {
        $dumper = new Dumper();
        $yaml = $dumper->dump($array);
        try {
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/../application/configs/application.yml', $yaml);
        } catch (DumpException $e) {
            printf("Unable to write file: %s", $e->getMessage());
            return false;
        }

    }

}
