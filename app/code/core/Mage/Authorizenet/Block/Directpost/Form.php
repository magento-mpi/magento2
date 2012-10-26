<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * DirectPost form block
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Authorizenet_Block_Directpost_Form extends Mage_Payment_Block_Form_Cc
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
        if ($this->getMethod()->getCode() != Mage::getSingleton('Mage_Authorizenet_Model_Directpost')->getCode()) {
            return null;
        }

        return parent::_toHtml();
    }

    /**
     * Set method info
     *
     * @return Mage_Authorizenet_Block_Directpost_Form
     */
    public function setMethodInfo()
    {
        $payment = Mage::getSingleton('Mage_Checkout_Model_Type_Onepage')
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
        return $this->getAction()
            ->getRequest()
            ->getParam('isAjax');
    }
}
