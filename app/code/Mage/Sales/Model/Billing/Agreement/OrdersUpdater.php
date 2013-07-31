<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders grid massaction items updater
 *
 * @category   Mage
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Billing_Agreement_OrdersUpdater implements Mage_Core_Model_Layout_Argument_UpdaterInterface
{

    /**
     * @var Mage_Core_Model_Registry
     */
    protected $_registryManager;
    /**
     * @param array $data
     * @throws InvalidArgumentException
     */
    public function __construct(array $data = array())
    {
        $this->_registryManager = isset($data['registry']) ?
            $data['registry'] :
            Mage::getSingleton('Mage_Core_Model_Registry');

        if (false === ($this->_registryManager instanceof Mage_Core_Model_Registry)) {
            throw new InvalidArgumentException('registry object has to be an instance of Mage_Core_Model_Registry');
        }
    }

    /**
     * Add billing agreement filter
     *
     * @param mixed $argument
     * @throws DomainException
     * @return mixed
     */
    public function update($argument)
    {
        $billingAgreement = $this->_registryManager->registry('current_billing_agreement');

        if (!$billingAgreement) {
            throw new DomainException('Undefined billing agreement object');
        }

        $argument->addBillingAgreementsFilter($billingAgreement->getId());
        return $argument;
    }
}
