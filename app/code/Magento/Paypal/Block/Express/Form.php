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
namespace Magento\Paypal\Block\Express;

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
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Paypal\Model\ConfigFactory $paypalConfigFactory
     * @param \Magento\Paypal\Helper\Data $paypalData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Paypal\Model\ConfigFactory $paypalConfigFactory,
        \Magento\Paypal\Helper\Data $paypalData,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        $this->_paypalData = $paypalData;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $paypalConfigFactory, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Set template and redirect message
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
     * @return bool
     */
    public function getBillingAgreementcode()
    {
        $customerId = $this->_customerSession->getCustomerId();
        return $this->_paypalData->shouldAskToCreateBillingAgreement($this->_config, $customerId)
            ? \Magento\Paypal\Model\Express\Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT : null;
    }
}
