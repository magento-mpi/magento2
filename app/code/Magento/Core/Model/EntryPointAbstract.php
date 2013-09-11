<?php
/**
 * Abstract application entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

abstract class EntryPointAbstract
{
    /**
     * Application configuration
     *
     * @var \Magento\Core\Model\Config\Primary
     */
    protected $_config;

    /**
     * Application object manager
     *
     * @var \Magento\Core\Model\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Core\Model\Config\Primary $config
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\Core\Model\Config\Primary $config, \Magento\ObjectManager $objectManager = null)
    {
        $this->_config = $config;
        $this->_objectManager = $objectManager;
    }

    /**
     * Process request by the application
     */
    public function processRequest()
    {
        $this->_init();
        $this->_processRequest();
    }

    /**
     * Initializes the entry point, so a Magento application is ready to be used
     */
    protected function _init()
    {
        if (!$this->_objectManager) {
            $this->_objectManager = new \Magento\Core\Model\ObjectManager($this->_config);
        }
    }

    /**
     * Template method to process request according to the actual entry point rules
     */
    protected abstract function _processRequest();
}

