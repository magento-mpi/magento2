<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Default Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Totals_Default extends Mage_Adminhtml_Block_Sales_Order_Create_Totals
{
    protected $_template = 'Mage_Adminhtml::sales/order/create/totals/default.phtml';

    /**
     * Retrieve quote session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Adminhtml_Model_Session_Quote');
    }

    /**
     * Retrieve store model object
     *
     * @return Mage_Core_Model_Store
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
