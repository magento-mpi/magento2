<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend abstract block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Magento_Backend_Block_Template extends Magento_Core_Block_Template
{
    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_authorization = $context->getAuthorization();
        parent::__construct($context, $data);
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage::getSingleton('Magento_Core_Model_Session')->getFormKey();
    }

    /**
     * Check whether or not the module output is enabled
     *
     * Because many module blocks belong to Backend module,
     * the feature "Disable module output" doesn't cover Admin area
     *
     * @param string $moduleName Full module name
     * @return boolean
     */
    public function isOutputEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->getModuleName();
        }
        return !$this->_coreStoreConfig->getConfigFlag('advanced/modules_disable_output/' . $moduleName);
    }
    
    /**
     * Make this public so that templates can use it properly with template engine
     * 
     * @return Magento_AuthorizationInterface
     */
    public function getAuthorization() 
    {
        return $this->_authorization;
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->_eventManager->dispatch('adminhtml_block_html_before', array('block' => $this));
        return parent::_toHtml();
    }
}
