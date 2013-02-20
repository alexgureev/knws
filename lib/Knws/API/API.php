<?php namespace Knws\API;
use \Knws\RPC as R;

class API
{
    public function __construct()
    {
        // Store log for debuging requests from AJAX
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/api.log', print_r($_REQUEST, 1));

        // Execute requested JSON
        $this->execute(json_decode($_REQUEST['json'], true));
    }

    protected function execute($request)
    {
        // Output result string 
        echo (R::Auth()->auth()) ? R::$request['method']()->api($request) : 'Acces restricted';
    }
}

?>
