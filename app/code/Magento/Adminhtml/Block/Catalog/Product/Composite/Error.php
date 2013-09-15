<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml block for showing product options fieldsets
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author    Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Composite_Error extends Magento_Core_Block_Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Returns error message to show what kind of error happened during retrieving of product
     * configuration controls
     *
     * @return string
     */
    public function _toHtml()
    {
        $message = $this->_coreRegistry->registry('composite_configure_result_error_message');
        return $this->_coreData->jsonEncode(array('error' => true, 'message' => $message));
    }
}
