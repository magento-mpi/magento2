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
class Mage_Install_Model_Installer_Config
{
    protected $_localConfigFile;
    
    public function __construct() 
    {
        $this->_localConfigFile = Mage::getBaseDir('etc').DS.'local.xml';
    }
    
    public function install()
    {
        $data = Mage::getSingleton('install', 'session')->getConfigData();
        foreach (Mage::getModel('core', 'config')->getLocalServerVars() as $index=>$value) {
            if (!isset($data[$index])) {
                $data[$index] = $value;
            }
        }
        $this->_checkHostsInfo($data);
        $data['date'] = date('r');
        file_put_contents($this->_localConfigFile, Mage::getModel('core', 'config')->getLocalDist($data));
        Mage::getConfig()->init();
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
            ->setSecurePort(443)
            ->setDbHost('localhost')
            ->setDbName('magento')
            ->setDbUser('root')
            ->setDbPass('');
        return $data;
    }
    
    protected function _checkHostsInfo($data)
    {
        $url = $data['protocol'] . '://' . $data['host'] . ':' . $data['port'] . $data['base_path'];
        $surl= $data['secure_protocol'] . '://' . $data['secure_host'] . ':' . $data['secure_port'] . $data['secure_base_path'];
        
        $reporting_level = error_reporting(E_ERROR);
        $checkRes = file_get_contents($url);
        if (!$checkRes) {
            Mage::getSingleton('install', 'session')->addMessage(
                Mage::getModel('core', 'message')->error(__('Url "%s" is not access', $url))
            );
            throw new Exception('Check url error');
        }
        
        $checkRes = file_get_contents($surl);
        if (!$checkRes) {
            Mage::getSingleton('install', 'session')->addMessage(
                Mage::getModel('core', 'message')->error(__('Url "%s" is not access', $surl))
            );
            throw new Exception('Check url error');
        }
        error_reporting($reporting_level);
        return $this;
    }
}