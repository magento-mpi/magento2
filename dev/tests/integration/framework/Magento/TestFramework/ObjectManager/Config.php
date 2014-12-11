<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\TestFramework\ObjectManager;

class Config extends \Magento\Framework\Interception\ObjectManager\Config\Developer
{
    /**
     * Clean configuration
     */
    public function clean()
    {
        $this->_preferences = [];
        $this->_virtualTypes = [];
        $this->_arguments = [];
        $this->_nonShared = [];
        $this->_mergedArguments = [];
    }
}
