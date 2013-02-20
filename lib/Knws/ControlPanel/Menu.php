<?php namespace Knws\ControlPanel;
use \Knws\RPC as R;

class Menu
{
    protected $content;
    protected $menuActive = 0;

    public function __construct()
    {
        $this->content = array(
            'navbar'        => array(
                array('title' => 'Home', 'url' => '/', 'class' => ''),
                array('title' => 'Crawler', 'url' => '/crawler/', 'class' => 'active'),
                array('title' => 'Home', 'url' => '/', 'class' => '')
                ),
            'menuItems'       => array(
                array('title' => 'Home', 'url' => '/', 'class' => ''),
                array('title' => 'Menu Manager', 'url' => '/', 'class' => ''),
                array('title' => 'Task Manager', 'url' => '/', 'class' => ''),
                array('title' => 'Crawler', 'url' => '/crawler/', 'class' => '')
                )
            );

        $this->setActive(3);
        return $this;
    }

    public function insert($pos, $array, $active = 0)
    {
        //$this->content['menuItems'] = array_merge(array_slice($this->content['menuItems'], 0, $pos, true), array($array), array_slice($this->content['menuItems'], $pos, count($this->content['menuItems']) - 1, true));
        array_splice($this->content['menuItems'], $pos, 0, array($array));

        if($active == 1)
        {
            $this->setActive($pos);
        }
        else
        {
            return $pos;
        }
    }

    public function setActive($id)
    {
        $this->content['menuItems'][$this->menuActive]['class'] = '';
        $this->content['menuItems'][$id]['class'] = 'active';
        $this->menuActive = $id;
    }

    public function build()
    {
        $this->insert(2, array('title' => 'Inserted to 2 0', 'url' => '/'), 1);
        return $this->content;
    }

}
?>
