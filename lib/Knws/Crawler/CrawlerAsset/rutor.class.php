<?php namespace Knws\Crawler;
use \Knws\RPC as R;

class CrawlerAsset
{
    protected function checkResult()
    {
        if($this->result[1]['url']=='http://www.rutor.org/d.php')
        {
            R::MongoDriver()->rutor->insert(array(
                'url'       => $this->task,
                'status'    => 'fail',
                'date'      => new \MongoDate()
            ));
        }
        elseif($this->result[1]['http_code']==404)
        {
            R::MongoDriver()->rutor->insert(array(
                'url'       => $this->task,
                'status'    => '404',
                'date'      => new \MongoDate()
            ));
        }
        else
        {
            R::MongoDriver()->rutor->insert(array(
                'url'       => $this->task,
                'result'    => $this->result[0],
                'status'    => 'done',
                'size'      => $this->result[1]['size_download'],
                'date'      => new \MongoDate()
            ));
        }
    }

    protected function clearTags()
    {
        //http://php.net/manual/en/function.strip-tags.php
        $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
               /*'@<[\/\!]*?[^<>]*?>@si',             Strip out HTML tags*/
               '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
               '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
        );

        $this->result[0] = preg_replace($search, '', $this->result[0]);
    }

    protected function executeTask()
    {
        $this->result = R::ProxySwitcher()->getUrl($this->task);
        $this->sliceTop('<div id="download">');
        $this->sliceBottom('<div style="text-align: center;">');
        $this->clearTags();
    }
}

?>