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
     * @var \Magento\Sales\Model\Billing\AgreementFactory
     */
    protected $_agreementFactory;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Sales\Model\Billing\AgreementFactory $agreementFactory
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Sales\Model\Billing\AgreementFactory $agreementFactory
    ) {
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
}
