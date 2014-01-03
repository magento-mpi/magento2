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
     * @var \Magento\Customer\Service\Entity\V1\CustomerBuilder
     */
    protected $_customerBuilder;

    /**
     * @param \Magento\Customer\Service\Entity\V1\CustomerBuilder $customerBuilder
     */
    public function __construct(
        \Magento\Customer\Service\Entity\V1\CustomerBuilder $customerBuilder
    ) {
        $this->_customerBuilder = $customerBuilder;
    }

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
        $this->_convertAttributesFromModel($this->_customerBuilder, $customerModel);
        $this->_customerBuilder->setCustomerId($customerModel->getId());
        $this->_customerBuilder->setFirstname($customerModel->getFirstname());
        $this->_customerBuilder->setLastname($customerModel->getLastname());
        $this->_customerBuilder->setEmail($customerModel->getEmail());
        return $this->_customerBuilder->create();
    }

    /**
     * Loads the values from a customer model
     *
     * @param \Magento\Customer\Service\Entity\V1\CustomerBuilder $customerBuilder
     * @param \Magento\Customer\Model\Customer $customerModel
     */
    protected function _convertAttributesFromModel($customerBuilder, $customerModel)
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

        $customerBuilder->populateWithArray($attributes);
    }

}
