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
namespace Magento\Paypal\Block\Standard;

class Form extends \Magento\Payment\Block\Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = \Magento\Paypal\Model\Config::METHOD_WPS;

    /**
     * Config model instance
     *
     * @var \Magento\Paypal\Model\Config
     */
    protected $_config;

    /**
     * Set template and redirect message
     */
    protected function _construct()
    {
        $this->_config = \Mage::getModel('Magento\Paypal\Model\Config')->setMethod($this->getMethodCode());
        $locale = \Mage::app()->getLocale();
        /** @var $mark \Magento\Core\Block\Template */
        $mark = \Mage::app()->getLayout()->createBlock('Magento\Core\Block\Template');
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
