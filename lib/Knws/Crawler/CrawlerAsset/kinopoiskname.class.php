<?php namespace Knws\Crawler;
use \Knws\RPC as R;

class CrawlerAsset
{
    protected function checkResult()
    {
        if($this->result[1]['url']=='http://www.kinopoisk.ru/level/404/')
        {
            R::TaskManager()->failHappend($this->task, '404');

            return 404;
        }
        elseif(strpos($this->result[1]['url'], 'error.kinopoisk.ru/?ht=')>0)
        {
            R::MongoDriver()->proxy->update(array('_id' => $this->proxy['_id']), array(
                    '$set' => array('status' => 'blocked')
                ));

            R::MongoDriver()->block->insert(array('date' => new \MongoDate(), 'proxy' => $this->proxy, 'task' => $this->task, 'userAgent' => RPC::ProxySwitcher()->lastUsedUA));

            return 'blocked';
        }
        elseif($this->result[1]['http_code']==404)
        {
            R::TaskManager()->failHappend($this->task, $this->result[1]['http_code']);

            return $this->result[1]['http_code'];
        }
        elseif($this->result[1]['http_code']==403)
        {
            R::TaskManager()->failHappend($this->task, $this->result[1]['http_code']);

            return 'fail';
        }
        else
        {
            //xdebug_start_trace('/home/www/knws.ru/test/crawler/test');

            $res = $this->separateData();


            if(is_array($res['content']))
            {
                // TODO: exclude no-matter data, use only index in memory
                $pl = R::MongoDriver()->people->findOne(array('source' => $this->task));

                if($pl == 'timeout')
                {
                    return 'fail';
                }

                if(!is_array($pl))
                {
                    $ins = array(
                        'source'       => $this->task,
                        'content'   => array(),
                        'date'      => new \MongoDate()
                    );

                    $res['people'] = array_merge($res['people'], $ins);

                    $id = R::MongoDriver()->people->insert($res['people']);

                    $_id = $id['_id'];

                    foreach($res['content'] as $key => $val)
                    {
                        $or[] = array('kpid' => $key);
                    }

                    $cids = R::MongoDriver()->content->find(array('$or' => $or), true);

                    foreach ($cids as $cid)
                    {
                        $_cids[] = $cid['_id'];

                        R::MongoDriver()->content->update(array('_id' => $cid['_id']), array(
                            '$addToSet' => array('people' => array('id' => $_id, 'role' => $res['content'][$cid['kpid']]['role']))
                            ));

                        unset($res['content'][$cid['kpid']]);
                    }

                    foreach($res['content'] as $key => $val)
                    {
                        $content[] = array(
                            'kpid'      => $key,
                            'name'      => array('en' => $val['enname'], 'ru' => $val['name']),
                            'year'      => $val['year'],
                            'rate'      => array('kpvotes' => $val['votes'], 'kprate' => $val['rate']),
                            'people'   =>  array(array('id' => $_id, 'role' => $val['role'])),
                            'date'      => new \MongoDate()
                            );
                    }

                    if(is_array($content))
                    {
                        $cid = R::MongoDriver()->content->batchInsert($content);

                        foreach ($cid as $ids)
                        {
                            $_cids[] = $ids['_id'];
                        }
                    }

                    R::MongoDriver()->people->update(array('_id' => $_id), array(
                        '$addToSet' => array('content' => $_cids)
                    ));
                    //print_r($_cids);
                    $px = R::MongoDriver()->proxy->update(array('_id' => $this->proxy['_id']), array(
                            '$inc' => array('success' => 1),
                            '$set' => array('time' => $this->result[1]['total_time'], 'checked' => new \MongoDate())
                        ));
                    //xdebug_stop_trace();
                }

                return 'success';
            }
            else
            {
                return 'fail';
            }
        }
    }

    protected function separateData()
    {

        $data = $this->result[0];
        $name = explode("\n", $data);
        $name = $name[0];

        $international = explode("\n", $data);
        $international = $international[7];

        $dob = explode('дата рождения', $data);
        if(sizeof($dob)==2)
        {
            $dob = explode('&bull;', $dob[1]);
            if(sizeof($dob)==2) $dob = strip_tags($dob[0]); else $dob = '-';
        }
        else
        {
            $dob = '-';
        }

        preg_match_all('/"\/name\/\d*\/#(\w*)"/', $data, $career);
        preg_match_all('/"#genre_(\d*)"/', $data, $jenre);

        $career = $career[1];
        $jenre = $jenre[1];

        $films = explode('<a href="/film/', $data);

        for($i=1;$i<=sizeof($films)-1; $i++)
        {
            $arr = explode('" >', $films[$i]);
            if(sizeof($arr)>1)
            {
                //print_r($arr);

                $ar = explode('/', $arr[0]);
                $film[$i]['id'] = $ar[0];

                $ar = explode('</a>', $arr[1]);
                $film[$i]['name'] = $ar[0];

                $year = explode(')&nbsp;(', $film[$i]['name']);

                if(sizeof($year)==2)
                {
                    $type = explode(' (', $year[0]);
                    $typer = $type[1];
                    $film[$i]['name'] = $type[0];

                    $year = str_replace(')', '', $year[1]);
                }
                else
                {
                    $year = explode('&nbsp;(', $film[$i]['name']);

                    if(sizeof($year) == 2)
                    {
                        $film[$i]['name'] = $year[0];
                        $year = str_replace(')', '', $year[1]);
                    }
                    else
                    {
                        $year = str_replace('<a name="film_year', '', $year[0]);
                        $year = str_replace('">', '', $year);
                    }
                }

                $film[$i]['year'] = $year;
                $film[$i]['type'] = $typer;

                if(sizeof($ar)==3) $j=2; else $j=1;
                $ar = explode('&nbsp;...&nbsp;', $ar[$j]);
                if(sizeof($ar)>1) $flag = 1;

                $enname = explode('&nbsp;', $ar[0]);
                if(sizeof($enname)>1||$flag!=1)
                {
                    $film[$i]['votes'] = trim($enname[0]);
                    $tt = explode("\n", $film[$i]['votes']);

                    if(sizeof($tt)>1)
                    {
                        unset( $film[$i]['votes']);
                    }
                    else
                    {
                        $film[$i]['rate'] =  strip_tags($film[$i]['name']);
                        unset( $film[$i]['name']);
                    }
                }
                else
                {
                    $film[$i]['enname'] = trim(str_replace('<a name="', '', $ar[0]));

                    $eee = explode("\n", $film[$i]['enname']);
                    $film[$i]['enname'] = $eee[0];
                    $role = explode("\n", $ar[1]);
                    $film[$i]['role'] = $role[0];
                }

            }

        }
        if(is_array($film))
        foreach ($film as $keys)
        {
            if(isset($keys['name'])) $movies_array[$keys['id']]['name'] = $keys['name'];
            if(isset($keys['enname'])) $movies_array[$keys['id']]['enname'] = $keys['enname'];
            if(isset($keys['role'])) $movies_array[$keys['id']]['role'] = trim($keys['role']);
            if(isset($keys['votes'])) $movies_array[$keys['id']]['votes'] = $keys['votes'];
            if(isset($keys['rate'])) $movies_array[$keys['id']]['rate'] = $keys['rate'];
            if(isset($keys['year'])) $movies_array[$keys['id']]['year'] = $keys['year'];
            if(isset($keys['type'])) $movies_array[$keys['id']]['type'] = $keys['type'];

        }
        else
        {
            $this->fail = true;
        }

        //echo $name.' '.$international.' '.$dob.'<br>';
        //print_r($career);
        //print_r($jenre);
        //print_r($film);
        //print_r($movies_array);
        //echo $data;

        $res['people']['name'][] = $name;
        $res['people']['name'][] = $international;
        $res['people']['dob'] = $dob;
        $res['people']['career'] = $career;
        $res['people']['jenre'] = $jenre;
        $res['content'] = $movies_array;

        return $res;
    }

    protected function clearTags()
    {
        //http://php.net/manual/en/function.strip-tags.php
        $search = array('@<script[^>]*?>.*?</script>@si',  '@<style[^>]*?>.*?</style>@si'
               /*'@<[\/\!]*?[^<>]*?>@si',             //Strip out HTML tags*/
               //'@<![\s\S]*?--[ \t\n\r]*>@'
               //'@<style[^>]*>(.*)<\/style>@'*/
        );

        $this->result[0] = preg_replace($search, '', $this->result[0]);
        $this->result[0] = trim(strip_tags($this->result[0], '<a>'));
    }

    protected function executeTask()
    {
        R::ProxySwitcher()->getProxyList();
        $this->proxy = R::ProxySwitcher()->getRandomProxy();
        $this->result = R::ProxySwitcher()->getUrl($this->task, $this->proxy);

        $this->result[0] = iconv('cp1251', 'utf8', $this->result[0]);

        $this->sliceTop('<div class="shadow">');
        $this->sliceBottom('#top');
        $this->clearTags();
    }
}

?>