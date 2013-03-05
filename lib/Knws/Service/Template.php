<?php
/**
 * Description of Template
 * @see http://knws.ru/docs/Service/Template Documentation of Knws/Service\Template.
 * @author Barif
 */
namespace Knws\Service;

class Template extends \Knws\Service
{
    protected static $instance;
    protected static $class;

    /**
     * init description
     * @see http://knws.ru/docs/Service/Template/init Documentation of Knws\Service/Template->init().
     * @return array $result
     */
    public static function init()
    {
        self::$class = self::getClassName(__CLASS__);
        $engine = '\Knws\Module\\' . \Knws\Instance::$config['config'][self::$class]['engine'];
        self::$instance = new $engine();
    }

    /**
     * render output
     * @see http://knws.ru/docs/Service/Template/render Documentation of Knws\Service\Template->render().
     * @return mixed
     */
    public static function render($template, $content)
    {
        return self::$instance->render($template, $content);
    }
}
