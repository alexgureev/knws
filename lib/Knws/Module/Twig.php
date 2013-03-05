<?php
/**
 * Description of Module/Twig
 * @see http://knws.ru/docs/Module/Twig Documentation of Knws\Module\Twig.
 * @author Barif
 */
namespace Knws\Module;

class Twig extends Module
{
    /**
     * init description
     * @see http://knws.ru/docs/Module/init Documentation of Knws\Module->init().
     * @return obj $objs
     */
    public static function init($config)
    {
        $loader = new \Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . '/../application/views/');
        return new \Twig_Environment($loader, array('cache' => $_SERVER['DOCUMENT_ROOT'] .'/../data/cache/'));
    }
}
