<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Block\Express;

/**
 * PayPal Standard payment "form"
 */
class Form extends \Magento\Paypal\Block\Standard\Form
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_methodCode = \Magento\Paypal\Model\Config::METHOD_WPP_EXPRESS;

    /**
     * Paypal data
     *
     * @var \Magento\Paypal\Helper\Data
     */
    protected $_paypalData;

    /**
     * @var \Magento\Customer\Service\V1\CustomerCurrentService
     */
    protected $currentCustomer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Paypal\Model\ConfigFactory $paypalConfigFactory
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Paypal\Helper\Data $paypalData
     * @param \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Paypal\Model\ConfigFactory $paypalConfigFactory,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Paypal\Helper\Data $paypalData,
        \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer,
        array $data = array()
    ) {
        $this->_paypalData = $paypalData;
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context, $paypalConfigFactory, $localeResolver, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Set template and redirect message
     *
     * @return null
     */
    protected function _construct()
    {
        $result = parent::_construct();
        $this->setRedirectMessage(__('You will be redirected to the PayPal website.'));
        return $result;
    }

    /**
     * Get billing agreement code
     *
     * @return string|null
     */
    public function getBillingAgreementCode()
    {
        $customerId = $this->currentCustomer->getCustomerId();
        return $this->_paypalData->shouldAskToCreateBillingAgreement($this->_config, $customerId)
            ? \Magento\Paypal\Model\Express\Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT : null;
    }
}
