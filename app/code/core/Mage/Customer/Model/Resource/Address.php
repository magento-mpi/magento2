<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer address entity resource model
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Resource_Address extends Mage_Eav_Model_Entity_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $resource = Mage::getSingleton('Mage_Core_Model_Resource');
        $this->setType('customer_address')->setConnection(
            $resource->getConnection('customer_read'),
            $resource->getConnection('customer_write')
        );
    }

    /**
     * Set default shipping to address
     *
     * @param Varien_Object $address
     * @return Mage_Customer_Model_Resource_Address
     */
    protected function _afterSave(Varien_Object $address)
    {
        if ($address->getIsCustomerSaveTransaction()) {
            return $this;
        }
        if ($address->getId() && ($address->getIsDefaultBilling() || $address->getIsDefaultShipping())) {
            $customer = Mage::getModel('Mage_Customer_Model_Customer')
                ->load($address->getCustomerId());

            if ($address->getIsDefaultBilling()) {
                $customer->setDefaultBilling($address->getId());
            }
            if ($address->getIsDefaultShipping()) {
                $customer->setDefaultShipping($address->getId());
            }
            $customer->save();
        }
        return $this;
    }

    /**
     * Check customer address before saving
     *
     * @param Varien_Object $address
     * @return Mage_Customer_Model_Resource_Address
     */
    protected function _beforeSave(Varien_Object $address)
    {
        parent::_beforeSave($address);

        if (!$address->getIgnoreValidation()) {
            $this->_validate($address);
        }

        return $this;
    }

    /**
     * Validate customer address entity
     *
     * @param Mage_Customer_Model_Customer $address
     * @throws Magento_Validator_Exception when validation failed
     */
    protected function _validate($address)
    {
        $validatorGroup = $address->isObjectNew() ? 'create' : 'update';

        $validatorFactory = Mage::getConfig()->getValidatorConfig();
        $validator = $validatorFactory
            ->getValidatorBuilder('customer_address', $validatorGroup)
            ->createValidator();

        if (!$validator->isValid($address)) {
            throw new Magento_Validator_Exception($validator->getMessages());
        }
    }
}
