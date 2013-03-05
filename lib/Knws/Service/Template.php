<?php
/**
 * Description of Template
 * @see http://knws.ru/docs/Service/Template Documentation of Knws/Service\Template.
 * @author Barif
 */
namespace Knws\Service;

class Template
{
    protected static $templateManager;

    /**
     * init description
     * @see http://knws.ru/docs/Service/Template/init Documentation of Knws\Service\Template->init().
     * @return void
     */
    public static function init()
    {
        $engine = '\Knws\Module\\' . \Knws\Instance::$config['config']['Template']['engine'];
        self::$templateManager = $engine::init(\Knws\Instance::$config['config']['Template']);
    }

    /**
     * render output
     * @see http://knws.ru/docs/Service/Template/render Documentation of Knws\Service\Template->render().
     * @return mixed
     */
    public static function render($template, $content)
    {
        return self::$templateManager->render($template, $content);
    }
}
