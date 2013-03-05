<?php
/**
 * Description of Service
 * @see http://knws.ru/docs/Service Documentation of Knws\Service.
 * @author Barif
 */
namespace Knws;

class Service
{
    /**
     * Return classname cleared from namespace
     * @see http://knws.ru/docs/Service/getClassName Documentation of Knws\Service->getClassName().
     * @param string $namespace
     * @return string $class
     */
    protected static function getClassName($namespace)
    {
        return join('', array_slice(explode('\\', $namespace), -1));
    }
}
