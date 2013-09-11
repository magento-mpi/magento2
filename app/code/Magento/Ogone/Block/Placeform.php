<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Ogone\Block;

class Placeform extends \Magento\Core\Block\Template
{

    /**
     * Get checkout session namespace
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckout()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session');
    }

    /**
     * Ogone payment APi instance
     *
     * @return \Magento\Ogone\Model\Api
     */
    protected function _getApi()
    {
        return \Mage::getSingleton('Magento\Ogone\Model\Api');
    }

    /**
     * Return order instance with loaded onformation by increment id
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function _getOrder()
    {
        if ($this->getOrder()) {
            $order = $this->getOrder();
        } else if ($this->getCheckout()->getLastRealOrderId()) {
            $order = \Mage::getModel('Magento\Sales\Model\Order')->loadByIncrementId($this->getCheckout()->getLastRealOrderId());
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
