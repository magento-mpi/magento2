<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Standard;

/**
 * PayPal Standard payment "form"
 */
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
     * @var \Magento\Paypal\Model\ConfigFactory
     */
    protected $_paypalConfigFactory;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Paypal\Model\ConfigFactory $paypalConfigFactory
     * @param \Magento\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Paypal\Model\ConfigFactory $paypalConfigFactory,
        \Magento\Locale\ResolverInterface $localeResolver,
        array $data = array()
    ) {
        $this->_paypalConfigFactory = $paypalConfigFactory;
        $this->_localeResolver = $localeResolver;
        parent::__construct($context, $data);
    }

    /**
     * Set template and redirect message
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_config = $this->_paypalConfigFactory->create()->setMethod($this->getMethodCode());
        /** @var $mark \Magento\View\Element\Template */
        $mark = $this->_layout->createBlock('Magento\View\Element\Template');
        $mark->setTemplate('Magento_Paypal::payment/mark.phtml')
            ->setPaymentAcceptanceMarkHref(
                $this->_config->getPaymentMarkWhatIsPaypalUrl($this->_localeResolver)
            )
            ->setPaymentAcceptanceMarkSrc(
                $this->_config->getPaymentMarkImageUrl($this->_localeResolver->getLocaleCode())
            );
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
