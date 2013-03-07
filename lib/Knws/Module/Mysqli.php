<?php
/**
 * Description of Mysqli
 * @see http://knws.ru/docs/Module/Mysqli Documentation of Knws\Module\Mysqli.
 * @author Barif
 */

namespace Knws\Module;

class Mysqli extends \Knws\Module
{
    protected $connection;
    protected $collection;
    protected $prefix;
    protected $query;

    /**
     * __construct description
     * @see http://knws.ru/docs/Module/Mysqli/__construct Documentation of Knws\Module\Mysqli->__construct().
     * @return array $result
     */
    public function __construct()
    {
        $this->prefix = \Knws\Instance::$config['config']['DB']['prefix'];
        $this->connect();
        return $this;
    }

    /**
     * Connect to MySQL server using mysqli driver
     * @see http://knws.ru/docs/Module/Mysqli/connect Documentation of Knws\Module\Mysqli->connect().
     * @return obj $this
     */
    public function connect()
    {
        $config = \Knws\Instance::$config['config']['DB'];
        $this->connection = new \mysqli($config['host'], $config['user'], $config['password'], $config['database']);
        $this->connection->set_charset("utf8");
        return $this;
    }

    /**
     * Magic method to set table from object call
     * @param string $collection Table name
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
        $this->query = array('collection' => $this->prefix . $collection);
        return $this;
    }

    /**
     * find description
     * @see http://knws.ru/docs/Module/Mysqli/find Documentation of Knws\Module/Mysqli->find().
     * @param mixed $array
     * @return array $result
     */
    public function find($args)
    {
        print_r($args);
        print_r($this->where($args));
        //$sql = 'SELECT * FROM ' . $args[0] . ' WHERE ' . $args[1] . ' = ' . $args[1];

        //echo $sql;
        /*$result = $this->connection->query($sql, MYSQLI_USE_RESULT);

        if ($result)
        {
            while ($row = $result->fetch_assoc())
            {
                $user_arr[] = $row;
            }

            $result->close();
            $this->connection->next_result();
        }

        return $user_arr;*/
    }

    /**
     * where description
     * @see http://knws.ru/docs/Module/Mysqli/where Documentation of Knws\Module\Mysqli->where().
     * @param array $array
     * @return string $string
     */
    public function where($array)
    {
        if(is_array($array)) {
            foreach($array as $key => $value) {
                echo $key .'-'. $value;
            }
            $size = sizeof($array) - 1;
            for($i = 0; $i <= $size; $i++) {
                if(sizeof($array[$i]) == 2) {
                    $string .= ' `' . $array[$i][0] . '` = "' . $array[$i][1] . '"';
                }

                if(sizeof($array[$i]) == 3) {
                    $string .= str_replace($array[$i][0], $array[$i][1], $array[$i][2]);
                }

                if($i<$size) { $string .= ', ';
                }
            }
        }

        return $string;
    }
}
