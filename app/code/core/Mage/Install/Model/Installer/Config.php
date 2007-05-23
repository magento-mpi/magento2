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
        $data = Mage::getSingleton('install', 'session')->getConfigData();
        foreach (Mage::getModel('core', 'config')->getLocalServerVars() as $index=>$value) {
            if (!isset($data[$index])) {
                $data[$index] = $value;
            }
        }
        file_put_contents($this->_localConfigFile, Mage::getModel('core', 'config')->getLocalDist($data));
    }
    
    /*public function installDefault()
    {
        $configSrc = Mage::getModel('core', 'config')->getLocalDist();
        file_put_contents($this->_localConfigFile, $configSrc);
    }    */
    
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
            ->setSecurePort(443)
            ->setDbHost('localhost')
            ->setDbName('magento')
            ->setDbUser('root')
            ->setDbPass('');
        return $data;
    }
}