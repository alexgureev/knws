<?php namespace Knws;

    class Core
    {
        protected $config;

        public function __construct($config)
        {
            $this->config = $config;
        }

        public function run()
        {
            switch(RPC::Vars()->section)
            {
                case 'controlPanel':
                {
                    RPC::ControlPanel(array('ns'=> 'Knws\\ControlPanel\\'))->run();
                    break;
                }

                default:
                {
                    if(RPC::Vars()->page!="")
                    {
                        RPC::Page(array('ns'=> 'Knws\\Content\\'))->run();
                    }
                }
            }
        }
    }
?>