<?php
// make sure there's no E_STRICT
error_reporting(E_ALL);

// just a shortcut
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// add PEAR lib in include_path if needed
$_includePath = get_include_path();
$_pearPhpDir = dirname(dirname(__FILE__)) . DS . 'pear' . DS . 'php';
if (strpos($_includePath, $_pearPhpDir) === false) {
    if (substr($_includePath, 0, 2) === '.' . PATH_SEPARATOR) {
        $_includePath = '.' . PATH_SEPARATOR . $_pearPhpDir . PATH_SEPARATOR . substr($_includePath, 2);
    } else {
        $_includePath = $_pearPhpDir . PATH_SEPARATOR . $_includePath;
    }
    set_include_path($_includePath);
}

// include necessary PEAR libs
require_once "PEAR.php";
require_once "PEAR/Frontend.php";
require_once "PEAR/Registry.php";
require_once "PEAR/Config.php";
require_once "PEAR/Command.php";
require_once "PEAR/Exception.php";

require_once dirname(__FILE__)."/Pear/Frontend.php";

class Varien_Pear
{
    protected $_config;

    protected $_registry;

    protected $_frontend;

    protected $_cmdCache = array();

    static protected $_instance;


    public function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function isSystemPackage($pkg)
    {
        return in_array($pkg, array('Archive_Tar', 'Console_Getopt', 'PEAR', 'Structures_Graph'));
    }

    public function getBaseDir()
    {
        return dirname(dirname(dirname(__FILE__)));
    }

    public function getPearDir()
    {
        return dirname(dirname(__FILE__)).DS.'pear';
    }

    public function getConfig()
    {
        if (!$this->_config) {
            $pear_dir = $this->getPearDir();

            $config = PEAR_Config::singleton();

            $config->set('preferred_state', 'alpha');
            $config->set('auto_discover', 1);

            $config->set('bin_dir', $pear_dir);
            $config->set('php_dir', $pear_dir.DS.'php');
            $config->set('download_dir', $pear_dir.DS.'download');
            $config->set('temp_dir', $pear_dir.DS.'temp');
            $config->set('data_dir', $pear_dir.DS.'data');
            $config->set('cache_dir', $pear_dir.DS.'cache');
            $config->set('test_dir', $pear_dir.DS.'tests');
            $config->set('doc_dir', $pear_dir.DS.'docs');

            foreach ($config->getKeys() as $key) {
                if (!(substr($key, 0, 5)==='mage_' && substr($key, -4)==='_dir')) {
                    continue;
                }
                $config->set($key, preg_replace('#^\.#', $this->getBaseDir(), $config->get($key)));
                #echo $key.' : '.$config->get($key).'<br>';
            }

            #$config->setRegistry($this->getRegistry());

            #PEAR_DependencyDB::singleton($config, $pear_dir.DS.'reg'.DS.'.depdb');

            PEAR_Frontend::setFrontendObject($this->getFrontend());

            #PEAR_Command::registerCommands(false, $pear_dir.DS.'php'.DS.'PEAR'.DS.'Command'.DS);

            $this->_config = $config;
        }
        return $this->_config;
    }

    public function getRegistry()
    {
        if (!$this->_registry) {
            $this->_registry = new PEAR_Registry($this->getPearDir().DS.'reg');
        }
        return $this->_registry;
    }

    public function getFrontend()
    {
        if (!$this->_frontend) {
            $this->_frontend = new Varien_Pear_Frontend;
        }
        return $this->_frontend;
    }

    public function getLog()
    {
        return $this->getFrontend()->getLog();
    }

    public function getOutput()
    {
        return $this->getFrontend()->getOutput();
    }

    public function run($command, $options=array(), $params=array())
    {
        if (empty($this->_cmdCache[$command])) {
            $cmd = PEAR_Command::factory($command, $this->getConfig());
            if ($cmd instanceof PEAR_Error) {
                return $cmd;
            }
            $this->_cmdCache[$command] = $cmd;
        } else {
            $cmd = $this->_cmdCache[$command];
        }
        $result = $cmd->run($command, $options, $params);
        return $result;
    }

    public function setRemoteConfig($uri) #$host, $user, $password, $path='', $port=null)
    {
        #$uri = 'ftp://' . $user . ':' . $password . '@' . $host . (is_numeric($port) ? ':' . $port : '') . '/' . trim($path, '/') . '/';
        $this->run('config-set', array(), array('remote_config', $uri));
        return $this;
    }
}
