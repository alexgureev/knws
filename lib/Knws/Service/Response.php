<?php
/**
 * Description of Responce
 * @see http://knws.ru/docs/Service/Responce Documentation of Knws\Service\Responce.
 * @author Barif
 */
namespace Knws\Service;

use Symfony\Component\HttpFoundation\Response as Resp;

class Response extends \Knws\Service
{
    protected static $instance;
    protected static $class;

    /**
     * Load new Responce instance
     * @see http://knws.ru/docs/Service/Request/init Documentation of Knws\Service\Request>init().
     * @return array $result
     */
    public static function init()
    {
        self::$instance = new Resp();
    }

    /**
     * setContent description
     * @see http://knws.ru/docs/Service/Responce/setContent Documentation of Knws\Service\Responce->setContent().
     * @param string $content
     * @return void
     */
    public static function setContent($content)
    {
        self::$instance->setContent($content);
    }

    /**
     * setStatusCode description
     * @see http://knws.ru/docs/Service/Responce/setStatusCode Documentation of Knws\Service/Responce->setStatusCode().
     * @param int $code
     * @return void
     */
    public static function setStatusCode($code)
    {
        self::$instance->setStatusCode($code);
    }

    /**
     * setHeaders description
     * @see http://knws.ru/docs/Service/Responce/setHeaders Documentation of Knws\Service/Responce->setHeaders().
     * @param string $argument
     * @param string $value
     * @return void
     */
    public static function setHeaders($argument, $value)
    {
        self::$instance->headers->set($argument, $value);
    }

    /**
     * send description
     * @see http://knws.ru/docs/Service/Responce/send Documentation of Knws\Service\Responce->send().
     * @return void print responce
     */
    public static function send()
    {
        self::$instance->send();
    }
}
