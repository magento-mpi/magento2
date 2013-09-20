<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\TestFramework\ObjectManager;

class Config extends \Magento\ObjectManager\Config\Config
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
