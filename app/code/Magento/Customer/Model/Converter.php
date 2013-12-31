<?php


namespace Magento\Customer\Model;

/**
 * Customer Model converter.
 *
 * Converts a Customer Model to a DTO.
 * TODO Remove this class after service refactoring is done and the model
 * is no longer needed outside of service.  Then this funciton could be moved to the
 * service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Converter
{
    /**
     * Convert a customer model to a customer entity
     *
     * @param \Magento\Customer\Model\Customer $customerModel
     * @throws \InvalidArgumentException
     * @return \Magento\Customer\Service\Entity\V1\Customer
     */
    public function createCustomerFromModel($customerModel)
    {
        if (!($customerModel instanceof \Magento\Customer\Model\Customer)) {
            throw new \InvalidArgumentException('customer model is invalid');
        }
        $customer = new \Magento\Customer\Service\Entity\V1\Customer();
        $customer->setCustomerId($customerModel->getId());
        $customer->setFirstName($customerModel->getFirstname());
        $customer->setLastName($customerModel->getLastname());
        $customer->setEmail($customerModel->getEmail());
        $this->_convertAttributesFromModel($customer, $customerModel);

        return $customer;
    }

    /**
     * Loads the values from a customer model
     *
     * @param \Magento\Customer\Service\Entity\V1\Customer $customer
     * @param \Magento\Customer\Model\Customer $customerModel
     */
    protected function _convertAttributesFromModel($customer, $customerModel)
    {
        $attributes = [];
        foreach ($customerModel->getAttributes() as $attribute) {
            $attrCode = $attribute->getAttributeCode();
            $value = $customerModel->getData($attrCode);
            if (null == $value) {
                continue;
            }
            $attributes[$attrCode] = $value;
        }

        $customer->setAttributes($attributes);
    }

}