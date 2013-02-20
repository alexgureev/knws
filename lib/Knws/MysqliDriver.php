<?php

namespace Knws;
use \Knws\RPC as R;

class MysqliDriver
{
    public $connection;

    public $isNew = true;
    public $role;
    public $name;


    public function __construct()
    {
        $this->connect();
    }

    public function connect()
    {
        $this->connection = new \mysqli('localhost', 'librusec', 'iYTUfNerZPeeOYSXDw5y', 'librusec');
        $this->connection->set_charset("utf8");
    }

    public function select($sql)
    {
        $result = $this->connection->query($sql, MYSQLI_USE_RESULT);

        if ($result)
        {
            while ($row = $result->fetch_assoc())
            {
                $user_arr[] = $row;
            }

            $result->close();
            $this->connection->next_result();
        }

        return $user_arr;
    }

    public function create()
    {
        if (!$this->isNew) throw new \Exception("User already created");
        if (!$this->role) $this->role = 'member';

        if (!$this->validate()) throw new \Exception("User is invalid");

        $this->save();
    }

    public function validate()
    {

    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function save()
    {

    }
}

