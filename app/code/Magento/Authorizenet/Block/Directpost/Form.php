<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * DirectPost form block
 *
 * @category   Magento
 * @package    Magento_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Authorizenet_Block_Directpost_Form extends Magento_Payment_Block_Form_Cc
{
    protected $_template = 'directpost/info.phtml';

    /**
     * Render block HTML
     * If method is not directpost - nothing to return
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getMethod()->getCode() != Mage::getSingleton('Magento_Authorizenet_Model_Directpost')->getCode()) {
            return null;
        }

        return parent::_toHtml();
    }

    /**
     * Set method info
     *
     * @return Magento_Authorizenet_Block_Directpost_Form
     */
    public function setMethodInfo()
    {
        $payment = Mage::getSingleton('Magento_Checkout_Model_Type_Onepage')
            ->getQuote()
            ->getPayment();
        $this->setMethod($payment->getMethodInstance());

        return $this;
    }

    /**
     * Get type of request
     *
     * @return bool
     */
    public function isAjaxRequest()
    {
        return $this
            ->getRequest()
            ->getParam('isAjax');
    }
}
