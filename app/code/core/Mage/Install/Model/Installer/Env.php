<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Environment installer
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Installer_Env extends Mage_Install_Model_Installer_Abstract
{
    public function __construct() {}

    public function install()
    {
        if (!$this->_checkPhpExtensions()) {
            throw new Exception();
        }
        return $this;
    }

    protected function _checkPhpExtensions()
    {
        $res = true;
        $config = Mage::getSingleton('Mage_Install_Model_Config')->getExtensionsForCheck();
        foreach ($config as $extension => $info) {
            if (!empty($info) && is_array($info)) {
                $res = $this->_checkExtension($info) && $res;
            }
            else {
                $res = $this->_checkExtension($extension) && $res;
            }
        }
        return $res;
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
                Mage::getSingleton('Mage_Install_Model_Session')->addError(
                    Mage::helper('Mage_Install_Helper_Data')->__('One of PHP Extensions "%s" must be loaded.', implode(',', $extension))
                );
                return false;
            }
        }
        elseif (!extension_loaded($extension)) {
            Mage::getSingleton('Mage_Install_Model_Session')->addError(
                Mage::helper('Mage_Install_Helper_Data')->__('PHP extension "%s" must be loaded.', $extension)
            );
            return false;
        }
        else {
            /*Mage::getSingleton('Mage_Install_Model_Session')->addError(
                Mage::helper('Mage_Install_Helper_Data')->__("PHP Extension '%s' loaded", $extension)
            );*/
        }
        return true;
    }
}
