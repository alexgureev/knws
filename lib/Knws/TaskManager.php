<?php namespace Knws;

class TaskManager
{
    protected $action;
    protected $thread;
    protected $asset;

    public function __construct()
    {
        if(isset($_GET['action']))
        {
            $this->action = $_GET['action'];
        }
    }

    protected function checkProcessingList()
    {
        // db.tasks.findOne({"asset":"kinopoiskname", 'processing.thread': 2});

        $task = RPC::MongoDriver()->tasks->findOne(array('asset' => $this->asset, 'processing.thread' => $this->thread));

        if(is_array($task))
        {
            for($i=0; $i<=sizeof($task['processing'])-1; $i++)
            {
                if($task['processing'][$i]['thread']==$this->thread)
                {
                    $id = $i;
                }
            }

            if($task['processing'][$id]['attempts']>=10)
            {
                $this->failHappend($task['processing'][$id]['task'], '10 attempts');
                $this->threadOver();

                //file_put_contents('./thread.txt', 'db.tasks.update({"asset":"'.$asset.'"}, {"$pull":{processing: {thread:'.$thread.'}}});');
                return $task['processing'][$id]['task'];
            }
            else
            {
                $this->incAttempt();
                return $task['processing'][$id]['task'];
            }
        }
        else
        {
            return false;
        }
    }

    public function failHappend($task, $reason)
    {
        RPC::MongoDriver()->fails->insert(array(
                'url'       => $task,
                'reason'    => $reason,
                'date'      => new \MongoDate()
            ));
    }

    public function incAttempt()
    {
        // db.tasks.update({asset: "kinopoiskname",'processing.thread': 3}, {$inc: {'processing.0.attempts':1}}); WRONG
        //
        // > db.tasks.update({asset: "kinopoiskname", 'processing.thread' : 20}, {$inc:{"processing.$.attempts": 1}});

        RPC::MongoDriver()->tasks->update(array('asset' => $this->asset, 'processing.thread' => $this->thread), array('$inc' => array('processing.$.attempts' => 1)));
    }

    public function threadOver()
    {
        // db.tasks.update({"asset":"kinopoiskname", 'processing.thread': 2}, {"$pull":{processing: {thread:2}}});
        RPC::MongoDriver()->tasks->update(array('asset' => $this->asset), array('$pull' => array('processing' => array('thread' => $this->thread))));
    }

    public function getTask($asset, $thread)
    {
        $this->asset = $asset;
        $this->thread = $thread;
        $check = $this->checkProcessingList();

        if(!$check)
        {
            $task = RPC::MongoDriver()->tasks->find(array('asset' => $this->asset));

            if(sizeof($task) == 0)
            {
                return false;
            }
            else
            {
                if($task['current']<$task['max'])
                {
                    $task['current']++;
                    //comment to don't update
                    RPC::MongoDriver()->tasks->update(array('asset' => $this->asset), $task);
                    $tsk = str_replace('{ID}', $task['current'], $task['pattern']);

                    RPC::MongoDriver()->tasks->update(array('asset' => $this->asset), array('$addToSet' => array('processing' => array('thread' => $this->thread, 'task' => $tsk, 'attempts' => 0))));

                    return $tsk;
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            return $check;
        }
    }

    protected function addTask()
    {
        RPC::MongoDriver()->tasks->insert(array(
            'name'      => 'Kinopoisk Name',
            'pattern'   => 'http://www.kinopoisk.ru/name/{ID}/',
            'asset'     => 'kinopoiskname',
            'current'   => '1',
            'max'       => '2598300'
        ));
    }

    public function run()
    {
        $this->initDB();

        switch($this->action)
        {
            case 'checkProxy':
            {
                RPC::ProxySwitcher()->run()->checkProxy();
                break;
            }

            case 'parseProxy':
            {
                RPC::ProxySwitcher()->run()->parseProxyList($_GET['source']);
                break;
            }

            case 'moveProxy':
            {
                RPC::ProxySwitcher()->run()->moveProxyesToProduction();
                break;
            }
        }
    }
}
?>