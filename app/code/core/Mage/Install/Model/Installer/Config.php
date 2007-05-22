<?php
/**
 * Config installer
 *
 * @package     Mage
 * @subpackage  Install
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Install_Model_Installer_Config extends Mage_Install_Model_Installer 
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function install()
    {
        //$this->_createLocalXml();
        $data = Mage::getSingleton('install', 'session')->getConfigData();
        
        $configSrc = file_get_contents($this->_localConfigFile);
        
        foreach ($data as $index => $value) {
            $configSrc = str_replace('{'.$index.'}', $value, $configSrc);
        }

        file_put_contents($this->_localConfigFile, $configSrc);
        $config = new Mage_Core_Config();
        $config->init();
        unlink($config->getCacheStatFileName());
    }
    
    public function installDefault()
    {
        $config = new Mage_Core_Config();
        $configSrc = $config->getLocalDist();
        file_put_contents($this->_localConfigFile, $configSrc);
    }    
    
    public function getFormData()
    {
        $data = new Varien_Object();
        $host = $_SERVER['HTTP_HOST'];
        $hostInfo = explode(':', $host);
        $host = $hostInfo[0];
        $port = !empty($hostInfo[1]) ? $hostInfo[1] : 80;
        
        $data->setServerPath(dirname(Mage::getBaseDir()))
            ->setHost($host)
            ->setBasePath(substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],'install/')))
            ->setSecureHost($host)
            ->setSecureBasePath(substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],'install/')))
            ->setPort($port)
            ->setScurePort(443);
        return $data;
    }
}