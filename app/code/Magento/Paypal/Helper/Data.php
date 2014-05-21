<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Helper;

use Magento\Paypal\Model\Billing\Agreement\MethodInterface;

/**
 * Paypal Data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Cache for shouldAskToCreateBillingAgreement()
     *
     * @var bool
     */
    protected static $_shouldAskToCreateBillingAgreement = null;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData;

    /**
     * @var \Magento\Paypal\Model\Billing\AgreementFactory
     */
    protected $_agreementFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreHelper;

    /**
     * @var \Magento\Backend\Model\Config
     */
    protected $_backendConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Paypal\Model\Billing\AgreementFactory $agreementFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param \Magento\Backend\Model\Config $backendConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Paypal\Model\Billing\AgreementFactory $agreementFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Core\Helper\Data $coreHelper,
        \Magento\Backend\Model\Config $backendConfig
    ) {
        parent::__construct($context);
        $this->_paymentData = $paymentData;
        $this->_agreementFactory = $agreementFactory;
        $this->_request = $request;
        $this->_coreHelper = $coreHelper;
        $this->_backendConfig = $backendConfig;
    }

    /**
     * Check whether customer should be asked confirmation whether to sign a billing agreement
     *
     * @param \Magento\Paypal\Model\Config $config
     * @param int $customerId
     * @return bool
     */
    public function shouldAskToCreateBillingAgreement(\Magento\Paypal\Model\Config $config, $customerId)
    {
        if (null === self::$_shouldAskToCreateBillingAgreement) {
            self::$_shouldAskToCreateBillingAgreement = false;
            if ($customerId && $config->shouldAskToCreateBillingAgreement()) {
                if ($this->_agreementFactory->create()->needToCreateForCustomer($customerId)) {
                    self::$_shouldAskToCreateBillingAgreement = true;
                }
            }
        }
        return self::$_shouldAskToCreateBillingAgreement;
    }

    /**
     * Retrieve available billing agreement methods
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @param \Magento\Sales\Model\Quote|null $quote
     * @return MethodInterface[]
     */
    public function getBillingAgreementMethods($store = null, $quote = null)
    {
        $result = array();
        foreach ($this->_paymentData->getStoreMethods($store, $quote) as $method) {
            if ($this->canManageBillingAgreements($method)) {
                $result[] = $method;
            }
        }
        return $result;
    }

    /**
     * Check whether payment method can manage billing agreements or not
     *
     * @param mixed $methodInstance
     * @return bool
     */
    public function canManageBillingAgreements($methodInstance)
    {
        return $methodInstance instanceof MethodInterface;
    }

    /**
     * Get selected merchant country code in system configuration
     *
     * @return string
     */
    public function getConfigurationCountryCode() {
        $countryCode  = $this->_request->getParam(\Magento\Paypal\Model\Config\StructurePlugin::REQUEST_PARAM_COUNTRY);
        if (is_null($countryCode) || preg_match('/^[a-zA-Z]{2}$/', $countryCode) == 0) {
            $countryCode = $this->_backendConfig->getConfigDataValue(
                \Magento\Paypal\Block\Adminhtml\System\Config\Field\Country::FIELD_CONFIG_PATH
            );
        }
        if (empty($countryCode)) {
            $countryCode = $this->_coreHelper->getDefaultCountry();
        }
        return $countryCode;
    }
}
