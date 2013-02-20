<?php namespace Knws;

class ThreadManager
{
    public function __construct()
    {
        // todo set CRAWLER_PATH
        return $this;
    }

    public function api($request)
    {
        switch ($request['action'])
        {
            case 'stop':        { $this->stopThread($request['thread']);    break;}
            case 'start':       { $result = $this->startThread($request['thread']);   break;}
            case 'restart':     { $this->restartThread($request['thread']); break;}
            case 'log':         { $result = $this->getLog($request['thread']);   break;}

        }

        return print_r($result,1);
    }

    protected function stopThread($id)
    {
        $pidFile = CRAWLER_PATH.'/Threads/'.$id.'.lock';
        $pid = @file_get_contents($pidFile);

        if($pid > 0)
        {
            exec('kill '.$pid);
        }

        @unlink($pidFile);
    }

    protected function getLog($id)
    {
        $log = @file_get_contents(CRAWLER_PATH.'/Threads/'.$id.'.log');

        if(empty($log))
        {
            return 'NULL';
        }
        else
        {
            $log = explode("\n", $log);
            for($i= sizeof($log)-2; $i>=0; $i--)
            {
                $newlog .= $log[$i]."<br>\n";
            }

            return $newlog;
        }
    }

    protected function startThread($id)
    {
        return RPC::ProxySwitcher()->getUrl('http://test.knws.ru/?section=crawler&asset=kinopoiskname&thread='.$id, NULL, NULL, 0, 1);
    }

    protected function restartThread($id)
    {
        $this->stopThread($id);
        $this->startThread($id);
    }

    protected function getLockFiles()
    {
        $result = false;
        $fileList = scandir(CRAWLER_PATH.'/Threads/');

        foreach ($fileList as $file)
        {
            $array = explode('.', $file);

            if($array[0]!=""&&$array[1]=="lock")
            {
                $result[$array[0]]['tid'] = $array[0];
            }
            elseif ($array[0]!=""&&$array[1]=="log")
            {
                $result[$array[0]]['time'] = filemtime(CRAWLER_PATH.'/Threads/'.$file);
            }
        }


        return $result;
    }

    public function elapsedTime($when)
    {
        $now = new \DateTime();
        $diff = $now->diff(new \DateTime(date('Y-m-d H:i:s', $when)));
        return $diff;
    }

    public function minutesPassed($when)
    {
        $time = $this->elapsedTime($when);
        $result = $time->format('%d')*24*60+$time->format('%h')*60+$time->format('%i');

        return $result;
    }

    public function returnData()
    {
        $itemsPerRow = 6;

        $item = 1;
        $row = 0;
        $thread = $this->getLockFiles();
        for($i=1; $i<=40; $i++)
        {
            if($item>=$itemsPerRow)
            {
                $row++;
                $item = 1;
            }

            $item++;

            if($thread[$i]['tid']=="")
            {
                $result = array('title' => '#'.$i, 'btn' => 'btn btn-danger threadStartStop', 'checked' => 'checked', 'class' => 'span2', 'value' => '{ "method": "ThreadManager", "action": "start", "thread": '.$i.'}');
            }
            else
            {
                $time = $this->minutesPassed($thread[$i]['time']);

                if($time>10)
                {
                    $restart = array('title2' => 'R', 'btn2' => 'btn threadRestart btn-warning', 'value2' => '{ "method": "ThreadManager", "action": "restart", "thread": '.$i.'}');
                }
                else
                {
                    $restart = array();
                }

                $result = array_merge(array('title' => '#'.$i, 'btn' => 'btn threadStartStop', 'class' => 'span2', 'value' => '{ "method": "ThreadManager", "action": "stop", "thread": '.$i.'}'), $restart);
            }

            $result = array_merge($result, array('log' => 'btn btn-primary getLog', 'value3' => '{ "method": "ThreadManager", "action": "log", "thread": '.$i.'}', 'title3' => 'L'));

            $body['itemsList'][$row][] = $result;
        }

        return $body;
    }
}

?>
