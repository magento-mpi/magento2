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
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Paypal\Model\ConfigFactory
     */
    protected $_paypalConfigFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Paypal\Model\ConfigFactory $paypalConfigFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Paypal\Model\ConfigFactory $paypalConfigFactory,
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
        /** @var $mark \Magento\Core\Block\Template */
        $mark = $this->_layout->createBlock('Magento\Core\Block\Template');
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
