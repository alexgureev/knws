<?php namespace Knws\Crawler;

class CrawlerAsset
{
    protected function checkResult()
    {
        if($this->result[1]['url']=='http://www.kinopoisk.ru/level/404/')
        {
            \Knws\RPC::TaskManager()->failHappend($this->task, '404');
            return 'fail';
        }
        else
        {
            return 'success';
        }
    }

    protected function executeTask()
    {
        \Knws\RPC::ProxySwitcher()->getProxyList();
        $this->proxy = \Knws\RPC::ProxySwitcher()->getRandomProxy();
        $this->result = \Knws\RPC::ProxySwitcher()->getUrl($this->task, $this->proxy);

    }
}

?>