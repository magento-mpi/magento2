<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Ogone_Block_Placeform extends Magento_Core_Block_Template
{

    /**
     * Get checkout session namespace
     *
     * @return Magento_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session');
    }

    /**
     * Ogone payment APi instance
     *
     * @return Magento_Ogone_Model_Api
     */
    protected function _getApi()
    {
        return Mage::getSingleton('Magento_Ogone_Model_Api');
    }

    /**
     * Return order instance with loaded onformation by increment id
     *
     * @return Magento_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if ($this->getOrder()) {
            $order = $this->getOrder();
        } else if ($this->getCheckout()->getLastRealOrderId()) {
            $order = Mage::getModel('Magento_Sales_Model_Order')->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
        } else {
            return null;
        }
        return $order;
    }

    /**
     * Get Form data by using ogone payment api
     *
     * @return array
     */
    public function getFormData()
    {
        return $this->_getApi()->getFormFields($this->_getOrder());
    }

    /**
     * Getting gateway url
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->_getApi()->getConfig()->getGatewayPath();
    }
}
