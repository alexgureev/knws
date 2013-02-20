<?php namespace Knws\Crawler;
use \Knws\RPC as R;
//use \Knws\MysqliDriver as M;

class LibRusEc
{
    public $host = 'http://lib.rus.ec/sql/';
    public $dir;
    public $extract;
    public $files = array(
            'libmag.sql', 'libmags.sql','libseq.sql', 'libjoinedbooks.sql','libgenre.sql',
            'libsrclang.sql','librate.sql', 'libgenremeta.sql','libseqs.sql', 'libbook.sql',
            'libgenres.sql', 'libquality.sql', 'libavtors.sql'
        );

    public function __construct()
    {
        $this->dir = CRAWLER_PATH.DIRECTORY_SEPARATOR.'librusec'.DIRECTORY_SEPARATOR;
        $this->extract = $this->dir.'extract'.DIRECTORY_SEPARATOR;

    }

    function sql2json($query)
    {
        $data_sql = mysql_query($query) or die("'';//" . mysql_error());// If an error has occurred,
                //    make the error a js comment so that a javascript error will NOT be invoked
        $json_str = ""; //Init the JSON string.

        if($total = mysql_num_rows($data_sql)) { //See if there is anything in the query
            $json_str .= "[\n";

            $row_count = 0;
            while($data = mysql_fetch_assoc($data_sql)) {
                if(count($data) > 1) $json_str .= "{\n";

                $count = 0;
                foreach($data as $key => $value) {
                    //If it is an associative array we want it in the format of "key":"value"
                    if(count($data) > 1) $json_str .= "\"$key\":\"$value\"";
                    else $json_str .= "\"$value\"";

                    //Make sure that the last item don't have a ',' (comma)
                    $count++;
                    if($count < count($data)) $json_str .= ",\n";
                }
                $row_count++;
                if(count($data) > 1) $json_str .= "}\n";

                //Make sure that the last item don't have a ',' (comma)
                if($row_count < $total) $json_str .= ",\n";
            }

            $json_str .= "]\n";
        }

        //Replace the '\n's - make it faster - but at the price of bad redability.
        $json_str = str_replace("\n","",$json_str); //Comment this out when you are debugging the script

        //Finally, output the data
        return $json_str;
    }

    public function test()
    {

        /*
        R::Vars()->content = array(
            'session'   => session_id(),
            'projectName'   => 'KNWS Control Panel',
            'pageTitle'     => 'Frontpage > KNWS Control Panel'
            );
*/
        //$_SESSION['id'] = session_id();

        //echo R::TemplateManager()->render('index', R::Vars()->content);

        unlink($this->dir.'log.txt');
        $this->writeLog('start');

        $this->databaseCompare();

        /*
        $this->getSourceFiles();
        $this->extractFiles();
        $this->import();
              */
    }

    public function databaseCompare()
    {
        R::MongoDriver()->selectDB('books');
        $this->compareAuthors();

    }

    public function compareAuthors()
    {
        $table = 'libavtors.sql';
        $lastId = $this->getLastId($table);
        $res = R::MysqliDriver()->select('SELECT * FROM `libavtors` WHERE `aid` > "'.$lastId.'" ORDER BY `aid` ASC LIMIT 0 , 30');

        foreach ($res as $author)
        {
            print_r($author);
            //$pl = R::MongoDriver()->people->findOne(array('source' => $this->task));

            if($author['MiddleName']=="")
            {
                $authorName = $author['FirstName'].' '.$author['MiddleName'].' '.$author['LastName'];
            }
            else
            {
                $authorName = $author['FirstName'].' '.$author['LastName'];
            }
            $author['lang'] = ($author['lang']=="") ? 'undefined' : $author['lang'];
            $id = R::MongoDriver()->people->insert(array(
                'name' =>
                    array($author['lang'] => $authorName,
                        'full' =>
                        array('FirstName' => $author['FirstName'], 'MiddleName' => $author['MiddleName'], 'LastName' => $author['LastName'], 'NickName' => $author['NickName'])),
                'source' => 'http://lib.rus.ec/a/'.$author['aid'],
                'meta' => array( 'crawler' => 'librusec', 'table' => $table, 'id' => $author['aid'] ),
                'date' => new \MongoDate()
                ));
            //$cids = R::MongoDriver()->content->find(array('$or' => $or), true);
            //R::MongoDriver()->content->update(array('_id' => $cid['_id']), array('$addToSet' => array('people' => array('id' => $_id, 'role' => $res['content'][$cid['kpid']]['role']))));
            //$cid = R::MongoDriver()->content->batchInsert($content);
        }

        $this->setLastId($table, $author['aid']);
    }

    public function getLastId($table)
    {
        return file_get_contents($this->dir.DIRECTORY_SEPARATOR.'lastid'.DIRECTORY_SEPARATOR.$table);
    }

    public function setLastId($table, $data)
    {
        file_put_contents($this->dir.DIRECTORY_SEPARATOR.'lastid'.DIRECTORY_SEPARATOR.$table, $data);
    }

    public function writeLog($log)
    {
        file_put_contents($this->dir.'log.txt', $log."\n", FILE_APPEND);
    }

    public function dlFile($url, $filename)
    {
        exec("wget $url -O $filename");
    }

    public function getSourceFiles()
    {
        foreach ($this->files as $file)
        {
            $this->writeLog('dl '.$this->host.$file.'.gz');
            $this->dlFile($this->host.$file.'.gz', $this->dir.$file.'.gz');
        }
    }

    public function extractFile($file, $path)
    {
        $this->writeLog("gzip -dc $file.gz > $path");
        exec("gzip -dc $file.gz > $path");
    }

    public function extractFiles()
    {
        foreach ($this->files as $file)
        {
            $this->writeLog('extract '.$this->dir.$file);
            $this->extractFile($this->dir.$file, $this->extract.$file);
        }
    }

    public function importDump($file)
    {
        //$this->writeLog("mysql -udump -pdump dump < ".$this->extract.$file);
        //exec("mysql -udump -pdump dump < ".$this->extract.$file);
        $this->writeLog("mysql -ulibrusec -piYTUfNerZPeeOYSXDw5y librusec < ".$this->extract.$file);
        exec("mysql -ulibrusec -piYTUfNerZPeeOYSXDw5y librusec < ".$this->extract.$file);
    }

    public function import()
    {
        foreach ($this->files as $file)
        {
            $this->writeLog('import '.$this->extract.$file);
            $this->importDump($file);
        }
    }
}

?>
