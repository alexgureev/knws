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
     * find description
     * @see http://knws.ru/docs/Module/Mysqli/find Documentation of Knws\Module/Mysqli->find().
     * @param mixed $array
     * @return array $result
     */
    public function find($args)
    {
        print_r($args);


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


}
