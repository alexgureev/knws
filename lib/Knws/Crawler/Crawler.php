<?php namespace Knws\Crawler;
use \Knws\RPC as R;

(is_file(ASSETS_PATH.'/'.$_GET['asset'].'.class.php')) ? include_once ASSETS_PATH.'/'.$_GET['asset'].'.class.php': $error = 1;

if(!$error)
{
    class Crawler extends CrawlerAsset
    {
        protected $asset;
        protected $thread;
        protected $tfile;
        protected $result;
        protected $task = false;
        protected $proxy;
        protected $time;
        protected $fail;

        public function __construct()
        {
            $this->asset = $_GET['asset'];

            date_default_timezone_set('Europe/Kiev');
            $this->time = date('s');

            if($_GET['thread']==NULL)
            {
                $this->thread = 0;
            }
            else
            {
                $this->thread = intval($_GET['thread']);
            }

            $this->tfile = CRAWLER_PATH.'/Threads/'.$this->thread;
            return $this;
        }

        protected function sliceTop($fragment, $pos=1)
        {
            $array = explode($fragment, $this->result[0]);
            $this->result[0] = $array[$pos];
        }

        protected function sliceBottom($fragment, $pos=0)
        {
            $array = explode($fragment, $this->result[0]);
            $this->result[0] = $array[$pos];
        }

        protected function checkThreadIsRunning()
        {
            return is_file($this->tfile.'.lock');
        }

        protected function stopThread()
        {
            $pidFile = $this->tfile.'.lock';
            $pid = @file_get_contents($pidFile);

            unlink($pidFile);

            if($pid > 0)
            {
                echo 'Dropped by server';
                exec('kill '.$pid);
            }
        }

        public function run()
        {
            $work = true;

            if($this->checkThreadIsRunning())
            {
                // If thread already started and we recieved command to "stop"
                if($_GET['action']=="stop")
                {
                    $this->stopThread();
                    @file_put_contents($this->tfile.'.log', file_get_contents($this->tfile.'.log').date('r')."\t".'Stopped at '."\n");
                }
                // Thread in use, then die
                else
                {
                    @file_put_contents($this->tfile.'.log', file_get_contents($this->tfile.'.log').date('r')."\t".'Arleady in use'."\n");
                    die('Thread already in use');
                }
            }
            else
            {
                //Trying stop, but thread not ran
                if($_GET['action']=="stop")
                {
                    @file_put_contents($this->tfile.'.log', file_get_contents($this->tfile.'.log').date('r')."\t".'Fail attempt to stop'."\n");
                    die('Thread wasnt ran');
                }
                else
                {
                    @file_put_contents($this->tfile.'.lock', getmypid());
                    @file_put_contents($this->tfile.'.log', file_get_contents($this->tfile.'.log').date('r')."\t".'Started'."\n");
                    ignore_user_abort(true);
                    set_time_limit(0);
                }
            }

                $j = 1;
            while($this->checkThreadIsRunning()&&$work===true)
            {

                if($this->task===false)
                {
                    $this->task = R::TaskManager()->getTask($this->asset, $this->thread);

                    if($this->task===false)
                    {
                        $this->stopThread();
                        $work = false;
                    }
                }

                if($this->task)
                {
                    $this->executeTask();
                    $result = $this->checkResult();


                    if($result == 'success')
                    {
                        @file_put_contents($this->tfile.'.log', file_get_contents($this->tfile.'.log').date('r')."\t".'Success '.$this->task."\n");
                        $this->task = false;
                        R::TaskManager()->threadOver($this->asset, $this->thread);

                    }
                    elseif($result == 404)
                    {
                        @file_put_contents($this->tfile.'.log', file_get_contents($this->tfile.'.log').date('r')."\t".'404 '.$this->task."\n");
                        $this->task = false;
                        R::TaskManager()->threadOver($this->asset, $this->thread);
                    }
                    else
                    {
                        // пробуем заново
                    }

                    //file_put_contents('./thread.txt', file_get_contents('./thread.txt')."\n".$this->thread."\nhttp://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."\n");
                }

                //print_r($this->result);
                $this->time = date('s');
                //echo $result;
                //1 time
                //$work = false;
            }
            //xdebug_stop_trace();
            @file_put_contents($this->tfile.'.log', file_get_contents($this->tfile.'.log').date('r')."\t".'Dropped'."\n");
            $this->stopThread();

        }
    }
}
?>