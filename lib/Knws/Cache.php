<?php namespace Knws;

class Cache
{
    protected $link;
    public $useCache = 0;

    public function __construct()
    {
        $this->link = new \Memcache();
        $this->link->pconnect('127.0.0.1', 11211);
    }

    public function set()
    {
        call_user_func_array(array($this->link, 'set'), func_get_args());
    }

    public function get($var)
    {
        return $this->link->get($var);
    }

    public function replace()
    {
        call_user_func_array(array($this->link, 'replace'), func_get_args());
    }

    public function flush()
    {
        $this->link->flush();
    }

    public function delete($key)
    {
        $this->link->delete($key);
    }
}

?>