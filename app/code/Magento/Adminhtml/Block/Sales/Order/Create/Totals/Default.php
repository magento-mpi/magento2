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
 * Default Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Totals_Default extends Magento_Adminhtml_Block_Sales_Order_Create_Totals
{
    protected $_template = 'Magento_Adminhtml::sales/order/create/totals/default.phtml';

    /**
     * Retrieve quote session object
     *
     * @return Magento_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return $this->_sessionQuote;
    }

    /**
     * Retrieve store model object
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_getSession()->getStore();
    }

    public function formatPrice($value)
    {
        return $this->getStore()->formatPrice($value);
    }
}
