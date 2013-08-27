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
    public function __construct(Magento_Core_Helper_Data $coreData, Magento_Core_Block_Template_Context $context, array $data = array())
    {
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
        $message = Mage::registry('composite_configure_result_error_message');
        return $this->_coreData->jsonEncode(array('error' => true, 'message' => $message));
    }
}
