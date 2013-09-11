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
namespace Magento\Authorizenet\Block\Directpost;

class Form extends \Magento\Payment\Block\Form\Cc
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
        if ($this->getMethod()->getCode() != \Mage::getSingleton('Magento\Authorizenet\Model\Directpost')->getCode()) {
            return null;
        }

        return parent::_toHtml();
    }

    /**
     * Set method info
     *
     * @return \Magento\Authorizenet\Block\Directpost\Form
     */
    public function setMethodInfo()
    {
        $payment = \Mage::getSingleton('Magento\Checkout\Model\Type\Onepage')
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
