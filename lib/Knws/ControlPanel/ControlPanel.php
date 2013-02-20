<?php namespace Knws\ControlPanel;
use \Knws\RPC as R;

class ControlPanel
{
    public function __construct()
    {

    }

    public function merge($array)
    {
        R::Vars()->content = array_merge(R::Vars()->content, $array);
    }

    public function run()
    {
        R::Vars()->content = array(
            'session'   => session_id(),
            'projectName'   => 'KNWS Control Panel',
            'pageTitle'     => 'Frontpage > KNWS Control Panel'
            );

        $_SESSION['id'] = session_id();
        $this->merge(R::ThreadManager()->returnData());
        $this->merge(R::Menu(array('ns'=> 'Knws\\ControlPanel\\'))->build());

        echo R::TemplateManager()->render('index', R::Vars()->content);
    }
}
?>
