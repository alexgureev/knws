<?php
/**
 * Description of DB
 * @see http://knws.ru/docs/Service/DB Documentation of Knws/Service\DB.
 * @author Barif
 */
namespace Knws\Service;

class DB extends \Knws\Service
{
    protected static $instance;
    protected static $class;

    /**
     * Initialize $class variable and load service instance
     * @see http://knws.ru/docs/Service/DB/init Documentation of Knws\Service\DB->init().
     * @return array $result
     */
    public static function init()
    {
        self::$class = self::getClassName(__CLASS__);
        $engine = '\Knws\Module\\' . \Knws\Instance::$config['config'][self::$class]['engine'];
        self::$instance = new $engine();
    }

    /**
     * Magic method to set table from object call
     * @param string $collection Table name
     */
    public static function __callStatic($collection, $arguments)
    {
        self::$instance->setCollection($collection);
        // if sizeof == 0 return $instance else call find with args
        return (sizeof($arguments) == 0) ? self::$instance : self::$instance->find($arguments);
    }

   /**
     * Search record(s) in database
     * @see http://knws.ru/docs/DB/find Documentation of Knws\DB->find().
     * @param array $query
     * @param array $limit
     * @param array $order
     * @return array $result
     */
    public static function find()
    {
        return self::$instance->find(func_get_args());
    }

}
