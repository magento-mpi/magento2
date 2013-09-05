<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_TestFramework_ObjectManager_Config extends \Magento\ObjectManager\Config\Config
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
        $this->_mergedPlugins = array();
        $this->_mergedArguments = array();
    }
}
