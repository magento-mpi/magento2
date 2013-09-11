<?php
/**
 * Customer address entity resource model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Resource;

class Address extends \Magento\Eav\Model\Entity\AbstractEntity
{
    /**
     * @var \Magento\Core\Model\Validator\Factory
     */
    protected $_validatorFactory;

    /**
     * Initialize object dependencies
     *
     * @param \Magento\Core\Model\Validator\Factory $validatorFactory
     * @param array $data
     */
    public function __construct(\Magento\Core\Model\Validator\Factory $validatorFactory, $data = array())
    {
        $this->_validatorFactory = $validatorFactory;
        parent::__construct($data);
    }

    /**
     * Resource initialization.
     */
    protected function _construct()
    {
        $resource = \Mage::getSingleton('Magento\Core\Model\Resource');
        $this->setType('customer_address')->setConnection(
            $resource->getConnection('customer_read'),
            $resource->getConnection('customer_write')
        );
    }

    /**
     * Set default shipping to address
     *
     * @param \Magento\Object $address
     * @return \Magento\Customer\Model\Resource\Address
     */
    protected function _afterSave(\Magento\Object $address)
    {
        if ($address->getIsCustomerSaveTransaction()) {
            return $this;
        }
        if ($address->getId() && ($address->getIsDefaultBilling() || $address->getIsDefaultShipping())) {
            $customer = \Mage::getModel('\Magento\Customer\Model\Customer')
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
     * @param \Magento\Object $address
     * @return \Magento\Customer\Model\Resource\Address
     */
    protected function _beforeSave(\Magento\Object $address)
    {
        parent::_beforeSave($address);

        $this->_validate($address);

        return $this;
    }

    /**
     * Validate customer address entity
     *
     * @param \Magento\Customer\Model\Customer $address
     * @throws \Magento\Validator\ValidatorException when validation failed
     */
    protected function _validate($address)
    {
        $validator = $this->_validatorFactory->createValidator('customer_address', 'save');

        if (!$validator->isValid($address)) {
            throw new \Magento\Validator\ValidatorException($validator->getMessages());
        }
    }
}
