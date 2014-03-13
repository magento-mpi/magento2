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
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\Entity\Attribute\Set $attrSetEntity
     * @param \Magento\Locale\FormatInterface $localeFormat
     * @param \Magento\Eav\Model\Resource\Helper $resourceHelper
     * @param \Magento\Validator\UniversalFactory $universalFactory
     * @param \Magento\Core\Model\Validator\Factory $validatorFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Entity\Attribute\Set $attrSetEntity,
        \Magento\Locale\FormatInterface $localeFormat,
        \Magento\Eav\Model\Resource\Helper $resourceHelper,
        \Magento\Validator\UniversalFactory $universalFactory,
        \Magento\Core\Model\Validator\Factory $validatorFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        $data = array()
    ) {
        $this->_validatorFactory = $validatorFactory;
        $this->_customerFactory = $customerFactory;
        parent::__construct($resource, $eavConfig, $attrSetEntity, $localeFormat, $resourceHelper, $universalFactory, $data);
    }

    /**
     * Resource initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $resource = $this->_resource;
        $this->setType('customer_address')->setConnection(
            $resource->getConnection('customer_read'),
            $resource->getConnection('customer_write')
        );
    }

    /**
     * Set default shipping to address
     *
     * @param \Magento\Object $address
     * @return $this
     */
    protected function _afterSave(\Magento\Object $address)
    {
        if ($address->getIsCustomerSaveTransaction()) {
            return $this;
        }
        if ($address->getId() && ($address->getIsDefaultBilling() || $address->getIsDefaultShipping())) {
            $customer = $this->_createCustomer()->load($address->getCustomerId());

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
     * @return $this
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
     * @param \Magento\Object $address
     * @return void
     * @throws \Magento\Validator\ValidatorException When validation failed
     */
    protected function _validate($address)
    {
        $validator = $this->_validatorFactory->createValidator('customer_address', 'save');

        if (!$validator->isValid($address)) {
            throw new \Magento\Validator\ValidatorException($validator->getMessages());
        }
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function _createCustomer()
    {
        return $this->_customerFactory->create();
    }
}
