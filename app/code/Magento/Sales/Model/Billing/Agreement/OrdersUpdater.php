<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders grid massaction items updater
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Billing\Agreement;

class OrdersUpdater implements \Magento\Core\Model\Layout\Argument\UpdaterInterface
{

    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registryManager;
    /**
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data = array())
    {
        $this->_registryManager = isset($data['registry']) ?
            $data['registry'] :
            \Mage::getSingleton('Magento\Core\Model\Registry');

        if (false === ($this->_registryManager instanceof \Magento\Core\Model\Registry)) {
            throw new \InvalidArgumentException('registry object has to be an instance of \Magento\Core\Model\Registry');
        }
    }

    /**
     * Add billing agreement filter
     *
     * @param mixed $argument
     * @throws \DomainException
     * @return mixed
     */
    public function update($argument)
    {
        $billingAgreement = $this->_registryManager->registry('current_billing_agreement');

        if (!$billingAgreement) {
            throw new \DomainException('Undefined billing agreement object');
        }

        $argument->addBillingAgreementsFilter($billingAgreement->getId());
        return $argument;
    }
}
