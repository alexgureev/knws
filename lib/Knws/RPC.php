<?php namespace Knws;

class RPC
{
    protected static $objects = array();
    protected static $slug;

    public static function deploy($method, $obj)
    {
        // Store linked object for using with RPC
        self::$objects[$method] = $obj;
    }

    public static function slug()
    {
        // Initialize slug and default methods
        self::$slug = array(
            'A' => array('class' => 'API', 'default' => 'execute'),
            'My' => array('class' => 'MysqliDriver', 'default' => 'select'),
            'a' => array('class' => 'Auth', 'default' => 'auth'),
        );
    }

    public static function __callStatic($method, $args)
    {
        $method = '\\Knws\\'.$method;
        // If object not initialized, create new instance and return it
        // Если аргументы передаются в конструктор, то рассматривать стоит array[0]
        return self::$objects[$method] = (array_key_exists($method, self::$objects)) ?  self::$objects[$method] : new $method($args);
    }

    public static function __callStatic2($method, $args)
    {
        // Check short slug and default method
        $resource = (array_key_exists($method, self::$slug)) ? self::$slug[$method] : false;
        $call = false;
        if($resource)
        {
            $method = $resource['class'];
            $call = $resource['class'];
        }
        // If namespace not given than use Knws namespace, else merge $method with given ns
        $method = (empty($args[0]['ns'])) ? 'Knws\\'.$method : $args[0]['ns'].$method;

        // If object not initialized, create new instance and return it
        self::$objects[$method] = (array_key_exists($method, self::$objects)) ?  self::$objects[$method] : new $method($args);

        if($call)
        {
            return call_user_func_array(array(self::$objects[$method], $call), $args);
        }
        else
        {
            return self::$objects[$method];
        }
    }

     protected static function bindParameters($mock, $params)
    {
        $reflectionClass = new \ReflectionClass($mock);

        foreach ($params as $param => $value) {
            if (!($value instanceof \Closure)) {
                $reflectionProperty = $reflectionClass->getProperty($param);
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($mock, $value);
                continue;
            }
            $mock->
                    expects(new \PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount)->
                    method($param)->
                    will(new \PHPUnit_Framework_MockObject_Stub_ReturnCallback($value));
        }

    }

    function catch_fatal_error()
    {
      // Getting Last Error
      $last_error =  error_get_last();

      // Check if Last error is of type FATAL
      if(isset($last_error['type']) && $last_error['type']==E_ERROR)
      {
        // Fatal Error Occurs
        // Do whatever you want for FATAL Errors
      }

    }
    //register_shutdown_function('catch_fatal_error'); 

}
?>
