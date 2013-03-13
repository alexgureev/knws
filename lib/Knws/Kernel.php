<?php
/**
 * Description of Kernel
 * @see http://knws.ru/docs/Kernel Documentation of Knws\Kernel.
 * @author Barif
 */

namespace Knws;

//use Knws\Class as Class;

class Kernel
{
    protected $bundles;

    protected $bundleMap;
    protected $container;
    protected $rootDir;
    protected $environment;
    protected $debug;
    protected $booted;
    protected $name;
    protected $startTime;
    protected $classes;
    protected $errorReportingLevel;

    const VERSION         = '0.0.2';

    /**
     * Constructor.
     *
     * @param string  $environment The environment
     * @param Boolean $debug       Whether to enable debugging or not
     *
     * @api
     */
    public function __construct($environment, $debug)
    {
        $this->environment = $environment;
        $this->debug = (Boolean) $debug;
        $this->booted = false;
        //$this->rootDir = $this->getRootDir();
        //$this->name = $this->getName();
        $this->classes = array();
        $this->bundles = array();

        if ($this->debug) {
            $this->startTime = microtime(true);
        }

        //$this->init();
    }

    public function init()
    {
        ini_set('display_errors', 0);

        if ($this->debug) {
            error_reporting(-1);

            DebugClassLoader::enable();
            ErrorHandler::register($this->errorReportingLevel);
            if ('cli' !== php_sapi_name()) {
                ExceptionHandler::register();
            } else {
                ini_set('display_errors', 1);
            }
        }
    }
}
