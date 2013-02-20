<?php namespace Knws;

class ProxySwitcher
{
    public $proxyList;
    public $userAgent;
    public $lastUsedUA;

    public function __construct()
    {
        $this->userAgentList();
    }


    public function getUrl( $url, $useProxy = NULL, $postData = NULL, $javascript_loop = 0, $timeout = 20)
    {
        $url = str_replace( "&amp;", "&", urldecode(trim($url)) );

        $ch = curl_init();

        if($useProxy==NULL)
        {
            $useProxy['ip'] = 'without';
        }

        $userAgent = $this->getRandomUserAgent();
        $this->lastUsedUA = $userAgent;

        curl_setopt( $ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_COOKIEJAR, "/tmp/crawler/{$useProxy['ip']}.txt");
        curl_setopt( $ch, CURLOPT_COOKIEFILE, "/tmp/crawler/{$useProxy['ip']}.txt");
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        if($postdata != NULL)
        {
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $postData);
        }

        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 20 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );

        if(is_array($useProxy)&&$useProxy['ip']!="without")
        {
            curl_setopt( $ch, CURLOPT_PROXY, $useProxy['ip'] );
            curl_setopt( $ch, CURLOPT_PROXYPORT, $useProxy['port'] );

            if($useProxy['type']=='HTTP')
            {
                curl_setopt( $ch, CURLOPT_PROXYTYPE,  'CURLPROXY_HTTP');
            }
            elseif($useProxy['type']=='SOCKS5')
            {
                curl_setopt( $ch, CURLOPT_PROXYTYPE,  'CURLPROXY_SOCKS5');
            }
        }

        $content = curl_exec( $ch );
        $response = curl_getinfo( $ch );

        curl_close ( $ch );
        if ($response['http_code'] == 301 || $response['http_code'] == 302)
        {
            ini_set("user_agent", $userAgent);
            error_reporting(0);
            if ( $headers = get_headers($response['url']) )
            {
                foreach( $headers as $value )
                {
                    if ( substr( strtolower($value), 0, 9 ) == "location:" )
                        return $this->getUrl( trim( substr( $value, 9, strlen($value) ) ) );
                }
            }
            error_reporting(1);
        }

        if (    ( preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value) ) &&
                $javascript_loop < 5
        )
        {
            return $this->getUrl( $value[1], $javascript_loop+1 );
        }
        else
        {
            return array( $content, $response );
        }
    }

    public function parseProxyList($source)
    {

        switch ($source)
        {
            case '1':
            {
                $this->parseSpysRu();
                break;
            }

            case '2':
            {
                /* Copy-paste from:
                                * http://www.freeproxylists.com/elite/1351083090.html
                                * http://www.my-proxy.com/free-proxy-list.html
                                */
                $this->parseIPFile();
            }
        }

    }

    public function moveProxyesToProduction()
    {
        RPC::MongoDriver()->selectDB('knws-test');
        $list = RPC::MongoDriver()->proxy->find(array('status' => 'NEW'), true);

        RPC::MongoDriver()->selectDB('knws');

        $existed = RPC::MongoDriver()->proxy->find(array(), true);

        foreach ($existed as $item)
        {
            $IPs[$item['ip']] = 1;
        }

        foreach ($list as $proxy)
        {
            if($IPs[$proxy['ip']]!=1)
            {
                $proxy['status'] = 'checked';
                $proxy['checked'] = new \MongoDate();

                $list = RPC::MongoDriver()->proxy->insert($proxy);
            }
        }
    }

    protected function parseIPFile()
    {
        ignore_user_abort(true);

        $proxyes = file_get_contents(CRAWLER_PATH.'/proxylist.txt');

        $array = explode("\n", $proxyes);

        $list = RPC::MongoDriver()->proxy->find(array(), true);

        foreach ($list as $item)
        {
            $IPs[$item['ip']] = 1;
        }

        foreach ($array as $item)
        {
            $test1 = explode("\t", $item);
            unset($proxy);

            if(sizeof($test1)==2)
            {
                if($IPs[$test1[0]]==NULL)
                {
                    $proxy['ip'] = $test1[0];
                    $proxy['port'] = $test1[1];
                    $proxy['type'] = 'HTTP';
                    $flag = "new";
                    $IPs[$test1[0]] = 1;

                }
                else
                {
                    $flag = "old";
                }
            }
            else
            {
                $test1 = explode(":", $item);

                if(sizeof($test1)==2)
                {
                    if($IPs[$test1[0]]==NULL)
                    {
                        $proxy['ip'] = $test1[0];
                        $proxy['port'] = $test1[1];
                        $proxy['type'] = 'HTTP';
                        $flag = "new";
                        $IPs[$test1[0]] = 1;
                    }
                    else
                    {
                        $flag = "old";
                    }
                }
                else
                {
                    $flag = "error";
                }
            }

            if($flag == "new")
            {

                set_time_limit(40);

                $result = $this->getUrl('http://knws.ru/crawler/ping.html', $proxy);

                if($result[0]==1)
                {
                    echo $proxy['ip']." — checked<br>\n";
                    RPC::MongoDriver()->proxy->insert(array('ip' => $proxy['ip'], 'port' => $proxy['port'], 'type' => $proxy['type'], 'checked' => new MongoDate(), 'status' => 'NEW', 'time' => $result[1]['total_time'], 'speed' => $result[1]['speed_download']));
                }
                else
                {
                    echo $proxy['ip']." — dead<br>\n";
                    RPC::MongoDriver()->proxy->insert(array('ip' => $proxy['ip'], 'port' => $proxy['port'], 'type' => $proxy['type'], 'checked' => new MongoDate(), 'status' => 'dead', 'time' => $result[1]['total_time'], 'speed' => $result[1]['speed_download']));
                }
            }
        }
    }

    protected function parseSpysRu()
    {
        for($i=0; $i<=19; $i++)
        {
            if($i==0) $url = 'http://spys.ru/proxies/'; else $url = 'http://spys.ru/proxies'.$i.'/';

            $resource = $this->getUrl($url);
            $resource[0] = iconv('cp1251', 'utf8', $resource[0]);

            if($resource[1]['http_code']==200)
            {
                $array = explode('onmouseover="this.style.background=\'#002424\'"', $resource[0]);
                //print_r($array);

                for($j=1; $j<=sizeof($array)-1; $j++)
                {
                    $str = '<tr'.$array[$j];
                    $str = str_replace('spy14>',    'spy14>|', $str);
                    $str = str_replace('<script ',  '|<script ', $str);
                    $str = str_replace('spy1>', 'spy1>|', $str);
                    $str = str_replace('spy5>', 'spy5>|', $str);
                    $str = str_replace('spy2>', 'spy2>|', $str);
                    $str = strip_tags($str);

                    //echo $str."\n";
                    $data = explode('|', $str);

                    $proxy = array('ip' => $data[2], 'type' => $data[5]);
                    //print_r($proxy);
                    RPC::MongoDriver()->proxy->insert($proxy);
                }
            }
        }
    }

    public function checkProxy()
    {
        //ini_set('max_execution_time', 3600);
        // TODO: Добавить проверку прокси из таск менеджера
        // array('status' => array('$exists' => false)
        //$rand = mt_rand(1, 30);
        // array('$or' => array(array('status' => 'done'), array('status' => 'fail'), array('status' => 'failed')))
        //$list = $this->db->proxy->find(array('$or' => array(array('status' => 'checked'))), true);

        date_default_timezone_set('Europe/Kiev');
        $date = new \MongoDate(mktime(date('G')-1, 0, 0, date('n'), date('j'), date('Y')));
        $list = RPC::MongoDriver()->proxy->find(array('checked' => array( '$lt' => $date)), true)->skip(2);

        foreach ($list as $item)
        {
            //$this->getPort($item);
            $result = $this->getUrl('http://knws.ru/crawler/ping.html', $item);

            if($result[0]==1)
            {
                echo $item['ip']." — checked<br>\n";
                RPC::MongoDriver()->proxy->update(array('_id' => $item['_id']), array('$set' => array('checked' => new \MongoDate(), 'status' => 'checked', 'time' => $result[1]['total_time'], 'speed' => $result[1]['speed_download'])));
            }
            else
            {
                echo $item['ip']." — dead<br>\n";
                RPC::MongoDriver()->proxy->update(array('_id' => $item['_id']), array('$set' => array('checked' => new \MongoDate(), 'status' => 'dead')));
            }
        }
    }

    protected function getPort($useProxy)
    {
        //$ports = array('3128', '808', '1080', '8081', '80', '8080', '3129', '6588', '9999', '443', '8118', '8001', '54321', '10080', '1337', '8888', '83', '82', '181', '20000', '8909', '4233', '8084', '63000', '6673');
        $ports = array('8080', '3128', '80', '82', '808', '8081', '8118', '443', '3129', '8001', '8888', '20000', '54321', '1080', '10080', '9999');
        $res = 'fail';

        foreach ($ports as $port => $val)
        {
            $useProxy['port'] = $val;

            $result = $this->getUrl('http://knws.ru/crawler/ping.html', NULL, $useProxy);

            //print_r($result);

            if($result[0]==1)
            {
                //print_r($result[1]);

                RPC::MongoDriver()->proxy->update(array('_id' => $useProxy['_id']), array('$set' => array('port' => $useProxy['port'], 'status' => 'done', 'time' => $result[1]['total_time'], 'speed' => $result[1]['speed_download'])));
                $res = 'done';
            }
        }

        if($res == 'fail')
        {
            RPC::MongoDriver()->proxy->update(array('_id' => $useProxy['_id']), array('$set' => array('status' => 'fail')));
        }
    }

    public function getProxyList()
    {
        $cursor = RPC::MongoDriver()->proxy->find(array('status' => 'checked'), true)->sort(array('time' => 1))->limit(150);

        foreach ($cursor as $proxy)
        {
            $this->proxyList[] = array('ip' => $proxy['ip'], 'port' => $proxy['port'], 'type' => $proxy['type'], '_id' => $proxy['_id']);
        }
    }

    protected function userAgentList()
    {
        $this->userAgent = array(
            #'Mozilla/5.0 (Linux; U; Android 1.6; en-us; eeepc Build/Donut) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1',
            #'Mozilla/5.0 (Linux; U; Android 2.1-update1; ru-ru; GT-I9000 Build/ECLAIR) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17',
            #'Mozilla/5.0 (Linux; U; Android 2.2; ru-ru; GT-I9000 Build/FROYO) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
            #'Mozilla/5.0 (Linux; U; Android 3.1; en-us; GT-P7510 Build/HMJ37) AppleWebKit/534.13 (KHTML, like Gecko) Version/4.0 Safari/534.13',
            #'Mozilla/5.0 (Linux; U; Android 2.1-update1 (7hero-astar9.3); ru-ru; HTC Legend Build/ERE27) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17',
            #'BlackBerry9000/5.0.0.93 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/179',
            #'Mozilla/5.0 (BlackBerry; U; BlackBerry 9900; en-US) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.0.0.261 Mobile Safari/534.11+',
            'Mozilla/5.0 (PlayBook; U; RIM Tablet OS 1.0.0; en-US) AppleWebKit/534.8+ (KHTML, like Gecko) Version/0.0.1 Safari/534.8+',
            'Mozilla/5.0 (Macintosh; U; PPC Max OS X Mach-O; en-US; rv:1.8.0.7) Gecko/200609211 Camino/1.0.3',
            'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_7; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.205 Safari/534.16',
            'Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.1.6) Gecko/2007072300 Iceweasel/2.0.0.6 (Debian-2.0.0.6-0etch1+lenny1)',
            'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
            'Mozilla/5.0 (compatible; Konqueror/4.3; Linux) KHTML/4.3.5 (like Gecko)',
            'Mozilla/5.0 (X11; U; Linux i686; cs-CZ; rv:1.7.12) Gecko/20050929',
            'Mozilla/5.0 (Windows; I; Windows NT 5.1; ru; rv:1.9.2.13) Gecko/20100101 Firefox/4.0',
            'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:16.0) Gecko/20120815 Firefox/16.0',
            'Opera/9.80 (Windows NT 6.1; U; ru) Presto/2.8.131 Version/11.10',
            'Opera/9.80 (Macintosh; Intel Mac OS X 10.6.7; U; ru) Presto/2.8.131 Version/11.10',
            #'Opera/9.80 (S60; SymbOS; Opera Mobi/499; U; ru) Presto/2.4.18 Version/10.00',
            #'Opera/9.60 (J2ME/MIDP; Opera Mini/4.2.14912/812; U; ru) Presto/2.4.15',
            #'Opera/9.80 (Android; Opera Mini/7.5.31657/28.2555; U; ru) Presto/2.8.119 Version/11.10',
            'Mozilla/5.0 (Macintosh; I; Intel Mac OS X 10_6_7; ru-ru) AppleWebKit/534.31+ (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1'
        );
    }

    public function getRandomProxy()
    {
        $size = sizeof($this->proxyList)-1;
        $rand = mt_rand(0, $size);

        return $this->proxyList[$rand];
    }

    public function getRandomUserAgent()
    {
        $size = sizeof($this->userAgent)-1;
        $rand = mt_rand(0, $size);

        return $this->userAgent[$rand];
    }
}

?>