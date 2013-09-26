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
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale;

    /**
     * @var Magento_Paypal_Model_ConfigFactory
     */
    protected $_paypalConfigFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Paypal_Model_ConfigFactory $paypalConfigFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Paypal_Model_ConfigFactory $paypalConfigFactory,
        array $data = array()
    ) {
        $this->_locale = $locale;
        $this->_paypalConfigFactory = $paypalConfigFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Set template and redirect message
     */
    protected function _construct()
    {
        $this->_config = $this->_paypalConfigFactory->create()->setMethod($this->getMethodCode());
        /** @var $mark Magento_Core_Block_Template */
        $mark = $this->_layout->createBlock('Magento_Core_Block_Template');
        $mark->setTemplate('Magento_Paypal::payment/mark.phtml')
            ->setPaymentAcceptanceMarkHref($this->_config->getPaymentMarkWhatIsPaypalUrl($this->_locale))
            ->setPaymentAcceptanceMarkSrc($this->_config->getPaymentMarkImageUrl($this->_locale->getLocaleCode()));
        // known issue: code above will render only static mark image
        $this->setTemplate('Magento_Paypal::payment/redirect.phtml')
            ->setRedirectMessage(
                __('You will be redirected to the PayPal website when you place an order.')
            )
            ->setMethodTitle('') // Output PayPal mark, omit title
            ->setMethodLabelAfterHtml($mark->toHtml());
        return parent::_construct();
    }

    /**
     * Payment method code getter
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }
}
