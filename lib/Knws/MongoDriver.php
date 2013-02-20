<?php namespace Knws;

    class MongoDriver
    {
        protected $connection;
        public $dataBase;
        public $cache;
        protected $collection;
        protected $slaveOk;
        protected $state;

        public function __construct()
        {
            $this->cache = RPC::Cache();
        }

        public function __get($collection)
        {
            $this->collection = $this->dataBase->selectCollection($collection);
            return $this;
        }

        protected function serverList($config)
        {
            $size = sizeof($config);
            $uri = "mongodb://";

            for($i=0; $i<$size; $i++)
            {
                $uri .= $config[$i]['host'].':'.$config[$i]['port'];

                if($i<$size-1) $uri .= ',';
            }

            return $uri;
        }

        public function connect($config)
        {
            $shards = $config['Mongo']['shard'];
            unset($config['Mongo']['db']);
            $this->connection = ($this->connection) ?  : new \Mongo($this->serverList($shards), $config['Mongo']);
            //$this->slaveOk = $config['Mongo']['setSlaveOkay'];
            $this->setTimeout(40000);
            $this->state = $this->connection->getHosts();
            return $this;
        }

        public function selectDB($db)
        {
            $this->dataBase = $this->connection->selectDB("{$db}");
            $this->dataBase->setSlaveOkay($this->slaveOk);
        }

        public function setTimeout($tm)
        {
            \MongoCursor::$timeout = $tm;
        }

        public function insert()
        {
            $args = func_get_args();
            $this->collection->insert($args[0], array('safe'=>true));

            return $args[0];
        }

        public function batchInsert()
        {
            $args = func_get_args();
            $this->collection->batchInsert($args[0], array('safe'=>true));

            return $args[0];
        }

        public function find()
        {
            $args = func_get_args();

            $cursor = $this->collection->find($args[0]);

            if($args[1]===true)
            {
                return $cursor;
            }
            else
            {
                foreach ($cursor as $value)
                {
                    return($value);
                }
            }
        }

        public function findOne()
        {
            $args = func_get_args();

            if($this->cache->useCache == 1)
            {
                $result = $this->cache->get(md5(print_r($args[0], 1)));

                if($result===false)
                {
                    try
                    {
                        $result = $this->collection->findOne($args[0]);
                        $this->cache->set(md5(print_r($args[0], 1)), $result);

                        return $result;
                    }
                    catch (\MongoCursorTimeoutException $e)
                    {
                        //file_put_contents(LIB_PATH.'/logs/exeptions.log', $e->getMessage()."\t\t".date('r')."\n");
                        //$result = $this->findOne($args);
                        return 'timeout';
                    }
                }
                else
                {
                    return $result;
                }
            }
            else
            {
                try
                {
                    $cursor = $this->collection->findOne($args[0]);

                    return $cursor;
                }
                catch (\MongoCursorTimeoutException $e)
                {
                    //file_put_contents(LIB_PATH.'/logs/exeptions.log', $e->getMessage()."\t\t".date('r')."\n");
                    //$result = $this->findOne($args);
                    return 'timeout';
                }
            }
        }

        public function update()
        {
            $args = func_get_args();
            return $this->collection->update($args[0], $args[1]);
        }

        public function remove()
        {
            $args = func_get_args();
            return $this->collection->remove($args[0], $args[1]);
        }

        public function aggregate()
        {
            $args = func_get_args();
            return $this->collection->aggregate($args[0]);
        }
    }
?>
