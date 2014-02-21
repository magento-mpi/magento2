<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Paypal Data helper
 */
namespace Magento\Paypal\Helper;

class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Cache for shouldAskToCreateBillingAgreement()
     *
     * @var bool
     */
    protected static $_shouldAskToCreateBillingAgreement = null;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData;

    /**
     * @var \Magento\Paypal\Model\Billing\AgreementFactory
     */
    protected $_agreementFactory;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Paypal\Model\Billing\AgreementFactory $agreementFactory
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Paypal\Model\Billing\AgreementFactory $agreementFactory
    ) {
        $this->_coreData = $coreData;
        $this->_paymentData = $paymentData;
        $this->_agreementFactory = $agreementFactory;
        parent::__construct($context);
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
     * @param mixed $store
     * @param \Magento\Sales\Model\Quote $quote
     * @return array
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
        return ($methodInstance instanceof \Magento\Paypal\Model\Billing\Agreement\MethodInterface);
    }

    /**
     * Return backend config for element like JSON
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function getElementBackendConfig(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $config = $element->getFieldConfig();
        if (!array_key_exists('backend_congif', $config)) {
            return false;
        }

        $config = $config['backend_congif'];
        if (isset($config['enable_for_countries'])) {
            $config['enable_for_countries'] = explode(',', str_replace(' ', '', $config['enable_for_countries']));
        }
        if (isset($config['disable_for_countries'])) {
            $config['disable_for_countries'] = explode(',', str_replace(' ', '', $config['disable_for_countries']));
        }
        return $this->_coreData->jsonEncode($config);
    }
}
