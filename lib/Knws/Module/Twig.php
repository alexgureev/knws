<?php
/**
 * Description of Module/Twig
 * @see http://knws.ru/docs/Module/Twig Documentation of Knws\Module\Twig.
 * @author Barif
 */
namespace Knws\Module;

class Twig extends \Knws\Module
{
    /**
     * init description
     * @see http://knws.ru/docs/Module/load Documentation of Knws\Module\Twig->load().
     * @return obj $instance
     */
    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . '/../application/views/');
        $this->instance = new \Twig_Environment($loader, array('cache' => $_SERVER['DOCUMENT_ROOT'] .'/../data/cache/'));
        return $this;
    }

    /**
     * Render html file using twig template an content array
     * @see http://knws.ru/docs/Module/Twig/render Documentation of Knws\Module/Twig->render().
     * @param string $template Template filename
     * @param array $content Content array
     * @return string $html
     */
    public function render($template, $content)
    {
        return $this->instance->render($template, $content);;
    }

}
