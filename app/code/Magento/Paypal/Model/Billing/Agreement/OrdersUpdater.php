<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Billing\Agreement;

/**
 * Orders grid massaction items updater
 */
class OrdersUpdater implements \Magento\View\Layout\Argument\UpdaterInterface
{
    /**
     * @var \Magento\Registry
     */
    protected $_registryManager;

    /**
     * @var \Magento\Paypal\Model\Resource\Billing\Agreement
     */
    protected $_agreementResource;

    /**
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Paypal\Model\Resource\Billing\Agreement $agreementResource
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\Registry $coreRegistry,
        \Magento\Paypal\Model\Resource\Billing\Agreement $agreementResource,
        array $data = array()
    ) {
        $this->_registryManager = isset($data['registry']) ? $data['registry'] : $coreRegistry;
        $this->_agreementResource = $agreementResource;

        if (false === $this->_registryManager instanceof \Magento\Registry) {
            throw new \InvalidArgumentException('registry object has to be an instance of \Magento\Registry');
        }
    }

    /**
     * Add billing agreement filter
     *
     * @param mixed $argument
     * @return mixed
     * @throws \DomainException
     */
    public function update($argument)
    {
        $billingAgreement = $this->_registryManager->registry('current_billing_agreement');

        if (!$billingAgreement) {
            throw new \DomainException('Undefined billing agreement object');
        }

        $this->_agreementResource->addOrdersFilter($argument, $billingAgreement->getId());
        return $argument;
    }
}
