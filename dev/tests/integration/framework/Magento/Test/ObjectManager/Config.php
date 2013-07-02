<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Test_ObjectManager_Config extends Magento_ObjectManager_Config
{
    /**
     * Clean configuration
     */
    public function clean()
    {
        $this->_preferences = array();
        $this->_virtualTypes = array();
        $this->_arguments = array();
        $this->_nonShared = array();
        $this->_plugins = array();
    }
}
