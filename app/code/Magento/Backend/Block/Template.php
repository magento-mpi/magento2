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
namespace Magento\Backend\Block;

class Template extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_authorization = $context->getAuthorization();
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return \Mage::getSingleton('Magento\Core\Model\Session')->getFormKey();
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
        return !\Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $moduleName);
    }
    
    /**
     * Make this public so that templates can use it properly with template engine
     * 
     * @return \Magento\AuthorizationInterface
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
