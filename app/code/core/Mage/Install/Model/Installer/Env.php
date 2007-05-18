<?php
/**
 * Environment installer
 *
 * @package     Mage
 * @subpackage  Install
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Install_Model_Installer_Env extends Mage_Install_Model_Installer 
{
    public function __construct() 
    {
        
    }
    
    public function install()
    {
        $this->_checkPhpExtensions();
    }
    
    protected function _checkPhpExtensions()
    {
        $config = Mage::getSingleton('install', 'config')->getExtensionsForCheck();
        foreach ($config as $extension => $info) {
            if (!empty($info) && is_array($info)) {
                $this->_checkExtension($info);
            }
            else {
                $this->_checkExtension($extension);
            }
        }
        return $this;
    }
    
    protected function _checkExtension($extension)
    {
        if (is_array($extension)) {
            $oneLoaded = false;
            foreach ($extension as $item) {
                if (extension_loaded($item)) {
                    $oneLoaded = true;
                }
            }
            
            if (!$oneLoaded) {
                Mage::getSingleton('install', 'session')->addMessage(
                    Mage::getModel('core', 'message')->error(__('One from PHP Extensions "%s" must be loaded', implode(',', $extension)))
                );
            }
        }
        elseif(!extension_loaded($extension)) {
                Mage::getSingleton('install', 'session')->addMessage(
                    Mage::getModel('core', 'message')->error(__('PHP Extension "%s" must be loaded', $extension))
                );
        }
        return $this;
    }
}