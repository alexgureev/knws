<?php namespace Knws\Content;
use \Knws\RPC as R;

class Page
{
    public function __construct()
    {

    }

    public function view()
    {
        R::Vars()->content = array(
            'projectName'   => 'KNWS Page view',
            'pageTitle'     => 'Frontpage > KNWS Control Panel'
            );

        $CP = R::ControlPanel(array('ns'=> 'Knws\\ControlPanel\\'));
        $pages = R::MongoDriver()->pages->findOne(array('slug' => R::Vars()->page));
        $CP->merge(array('pages' => $pages));
        $CP->merge(R::Menu(array('ns'=> 'Knws\\ControlPanel\\'))->build());
        $TM = R::TemplateManager();

        echo $TM->render('index', R::Vars()->content);
    }

    public function create()
    {
        if(R::a())
        {
            $data = json_decode(R::Vars()->pageData, true);

            if(sizeof($data)>0)
            {
                R::MongoDriver()->pages->insert($data);
            }
        }
    }

    public function run()
    {
        $this->create();
        $this->view();
    }
}

?>
