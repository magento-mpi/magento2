<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Standard payment "form"
 */
class Magento_Paypal_Block_Standard_Form extends Magento_Payment_Block_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = Magento_Paypal_Model_Config::METHOD_WPS;

    /**
     * Config model instance
     *
     * @var Magento_Paypal_Model_Config
     */
    protected $_config;

    /**
     * Set template and redirect message
     */
    protected function _construct()
    {
        $this->_config = Mage::getModel('Magento_Paypal_Model_Config')->setMethod($this->getMethodCode());
        $locale = Mage::app()->getLocale();
        /** @var $mark Magento_Core_Block_Template */
        $mark = Mage::app()->getLayout()->createBlock('Magento_Core_Block_Template');
        $mark->setTemplate('Magento_Paypal::payment/mark.phtml')
            ->setPaymentAcceptanceMarkHref($this->_config->getPaymentMarkWhatIsPaypalUrl($locale))
            ->setPaymentAcceptanceMarkSrc($this->_config->getPaymentMarkImageUrl($locale->getLocaleCode()))
        ; // known issue: code above will render only static mark image
        $this->setTemplate('Magento_Paypal::payment/redirect.phtml')
            ->setRedirectMessage(
                __('You will be redirected to the PayPal website when you place an order.')
            )
            ->setMethodTitle('') // Output PayPal mark, omit title
            ->setMethodLabelAfterHtml($mark->toHtml())
        ;
        return parent::_construct();
    }

    /**
     * Payment method code getter
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }
}
