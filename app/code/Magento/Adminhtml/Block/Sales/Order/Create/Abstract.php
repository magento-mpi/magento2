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
 * Adminhtml sales order create abstract block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Adminhtml_Block_Sales_Order_Create_Abstract extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Retrieve create order model object
     *
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    public function getCreateOrderModel()
    {
        return Mage::getSingleton('Magento_Adminhtml_Model_Sales_Order_Create');
    }

    /**
     * Retrieve quote session object
     *
     * @return Magento_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote');
    }

    /**
     * Retrieve quote model object
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * Retrieve customer model object
     *
     * @return Magento_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->_getSession()->getCustomer();
    }

    /**
     * Retrieve customer identifier
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_getSession()->getCustomerId();
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

    /**
     * Retrieve store identifier
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_getSession()->getStoreId();
    }

    /**
     * Retrieve formated price
     *
     * @param   decimal $value
     * @return  string
     */
    public function formatPrice($value)
    {
        return $this->getStore()->formatPrice($value);
    }

    public function convertPrice($value, $format=true)
    {
        return $this->getStore()->convertPrice($value, $format);
    }
}
