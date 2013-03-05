<?php
/**
 * Description of Request
 * @see http://knws.ru/docs/Service/Request Documentation of Knws\Service\Request.
 * @author Barif
 */

namespace Knws\Service;

use Symfony\Component\HttpFoundation\Request;

class Request extends \Knws\Service
{
    protected static $instance;
    protected static $class;

    /**
     * Initialize $class variable and load service instance
     * @see http://knws.ru/docs/Service/Request/init Documentation of Knws\Service\Request>init().
     * @return array $result
     */
    public static function init()
    {
        self::$instance = Request::createFromGlobals();
    }

    /**
     * retrieve GET and POST variables respectively
     * @see http://knws.ru/docs/Service/Request/query Documentation of Knws\Service\Request->query().
     * @return obj self::$instance->query
     */
    public static function query()
    {
        return self::$instance->query;
    }

    /**
     * retrieve GET and POST variables respectively
     * @see http://knws.ru/docs/Service/Request/request Documentation of Knws\Service\Request->request().
     * @return obj self::$instance->request
     */
    public static function request()
    {
        return self::$instance->request;
    }

    /**
     * retrieve SERVER variables
     * @see http://knws.ru/docs/Service/Request/server Documentation of Knws\Service\Request->server().
     * @return obj self::$instance->server
     */
    public static function server()
    {
        return self::$instance->server;
    }

    /**
     * retrieves an instance of UploadedFile identified by foo
     * @see http://knws.ru/docs/Service/Request/files Documentation of Knws\Service\Request->files().
     * @return obj self::$instance->files
     */
    public static function files()
    {
        return self::$instance->files;
    }

    /**
     * retrieve a COOKIE value
     * @see http://knws.ru/docs/Service/Request/cookies Documentation of Knws\Service\Request->cookies().
     * @return obj self::$instance->cookies
     */
    public static function cookies()
    {
        return self::$instance->cookies;
    }

    /**
     * retrieve an HTTP request header, with normalized, lowercase keys
     * @see http://knws.ru/docs/Service/Request/headers Documentation of Knws\Service\Request->headers().
     * @return obj self::$instance->headers
     */
    public static function headers()
    {
        return self::$instance->headers;
    }

    /**
     * the URI being requested (e.g. /about) minus any query parameters
     * @see http://knws.ru/docs/Service/Request/getPathInfo Documentation of Knws\Service\Request->getPathInfo().
     * @return string return /path
     */
    public static function getPathInfo()
    {
        return self::$instance->getPathInfo();
    }

    /**
     * an array of languages the client accepts
     * @see http://knws.ru/docs/Service/Request/getLanguages Documentation of Knws\Service\Request->getLanguages().
     * @return array accepted languages
     */
    public static function getLanguages()
    {
        return self::$instance->getLanguages();
    }

    /**
     * GET, POST, PUT, DELETE, HEAD
     * @see http://knws.ru/docs/Service/Request/getMethod Documentation of Knws\Service\Request->getMethod().
     * @return array accepted languages
     */
    public static function getMethod()
    {
        return self::$instance->getMethod();
    }
}
