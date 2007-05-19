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
        $this->_createLocalXml();
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
    
    public function getFormData()
    {
        $data = new Varien_Object();
        $data->setServerPath(dirname(Mage::getBaseDir()))
            ->setHost($_SERVER['HTTP_HOST'])
            ->setBasePath(substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],'install/')))
            ->setSecureHost($_SERVER['HTTP_HOST'])
            ->setSecureBasePath(substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'],'install/')))
            ->setPort(80)
            ->setScurePort(443);
        return $data;
    }
}