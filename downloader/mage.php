<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', dirname(__DIR__));
define('MAGENTO_ROOT', dirname(__DIR__));

class __cli_Magento_Connect
{
    private static $_instance;
    protected $argv;
    public static function instance()
    {
        if(!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function init($argv)
    {
                $this->argv = $argv;
        $this->setIncludes();
        require_once("Mage/Autoload/Simple.php");
        Magento_Autoload_Simple::register();
        chdir(BP . DS . 'downloader' . DS);
        return $this;
    }

    public function setIncludes()
    {
        if (defined('DEVELOPMENT_MODE')) {
            $libPath = PS . dirname(BP) . DS . 'lib';
        } else {
            $libPath = PS . BP . DS . 'downloader' . DS . 'lib';
        }
        $includePath = BP . DS . 'app'
        . $libPath
        . PS . get_include_path();
        set_include_path($includePath);
    }



    public function getCommands()
    {
        return Magento_Connect_Command::getCommands();
    }

    public function getFrontend()
    {
        $frontend = Magento_Connect_Frontend::getInstance('CLI');
        Magento_Connect_Command::setFrontendObject($frontend);
        return $frontend;
    }

    public function getConfig($fileName = 'connect.cfg')
    {
        if (isset($this->config)) {
            return $this->config;
        }
        $config = new Magento_Connect_Config($fileName);
        if (empty($config->magento_root)) {
           $config->magento_root = dirname(__DIR__);
        }
        Magento_Connect_Command::setConfigObject($config);
        $this->config = $config;
        return $config;
    }

    public function detectCommand()
    {
        $argv = $this->argv;
        if(empty($argv[1])) {
            return false;
        }
        if(in_array($argv[1], $this->validCommands)) {
            list($options,$params) = $this->parseCommandArgs($argv);
            return array('name' => strtolower($argv[1]), 'options'=>$options, 'params'=>$params);
        }
        return false;
    }

    public function parseCommandArgs($argv)
    {
        $a = new Magento_System_Args();
        $args = $a->getFiltered();
        array_shift($args);
        return array($a->getFlags(), $args);
    }

    public function runCommand($cmd, $options, $params)
    {
        $c = Magento_Connect_Command::getInstance($cmd);
        $c->run($cmd, $options, $params);
    }

    private $_sconfig;
    public function getSingleConfig()
    {
        if(!$this->_sconfig) {
            $this->_sconfig = new Magento_Connect_Singleconfig(
                    $this->getConfig()->magento_root . DS .
                    $this->getConfig()->downloader_path . DS .
                    Magento_Connect_Singleconfig::DEFAULT_SCONFIG_FILENAME
            );
        }
        Magento_Connect_Command::setSconfig($this->_sconfig);
        return $this->_sconfig;
    }

    public function run()
    {
        $this->commands = $this->getCommands();
        $this->frontend = $this->getFrontend();
        $this->config = $this->getConfig();
        $this->validCommands = array_keys($this->commands);
        $this->getSingleConfig();
        $cmd = $this->detectCommand();
        if(!$cmd) {
            $this->frontend->outputCommandList($this->commands);
        } else {
            $this->runCommand($cmd['name'], $cmd['options'], $cmd['params']);
        }

    }

}

if (defined('STDIN') && defined('STDOUT') && (defined('STDERR'))) {
    __cli_Magento_Connect::instance()->init($argv)->run();
}
