<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer address model
 *
 * @method int getParentId() getParentId()
 * @method \Magento\Customer\Model\Address setParentId() setParentId(int $parentId)
 */
namespace Magento\Customer\Model;

class Address extends \Magento\Customer\Model\Address\AbstractAddress
{
    /**
     * Customer entity
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_customerFactory = $customerFactory;
        parent::__construct(
            $eventManager, $directoryData, $context, $registry, $eavConfig, $addressConfig, $regionFactory,
            $countryFactory, $resource, $resourceCollection, $data
        );
    }

    protected function _construct()
    {
        $this->_init('Magento\Customer\Model\Resource\Address');
    }

    /**
     * Retrieve address customer identifier
     *
     * @return integer
     */
    public function getCustomerId()
    {
        return $this->_getData('customer_id') ? $this->_getData('customer_id') : $this->getParentId();
    }

    /**
     * Declare address customer identifier
     *
     * @param integer $id
     * @return \Magento\Customer\Model\Address
     */
    public function setCustomerId($id)
    {
        $this->setParentId($id);
        $this->setData('customer_id', $id);
        return $this;
    }

    /**
     * Retrieve address customer
     *
     * @return \Magento\Customer\Model\Customer|bool
     */
    public function getCustomer()
    {
        if (!$this->getCustomerId()) {
            return false;
        }
        if (empty($this->_customer)) {
            $this->_customer = $this->_createCustomer()
                ->load($this->getCustomerId());
        }
        return $this->_customer;
    }

    /**
     * Specify address customer
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return \Magento\Customer\Model\Address
     */
    public function setCustomer(\Magento\Customer\Model\Customer $customer)
    {
        $this->_customer = $customer;
        $this->setCustomerId($customer->getId());
        return $this;
    }

    /**
     * Delete customer address
     *
     * @return \Magento\Customer\Model\Address
     */
    public function delete()
    {
        parent::delete();
        $this->setData(array());
        return $this;
    }

    /**
     * Retrieve address entity attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = $this->getData('attributes');
        if (is_null($attributes)) {
            $attributes = $this->_getResource()
                ->loadAllAttributes($this)
                ->getSortedAttributes();
            $this->setData('attributes', $attributes);
        }
        return $attributes;
    }

    public function __clone()
    {
        $this->setId(null);
    }

    /**
     * Return Entity Type instance
     *
     * @return \Magento\Eav\Model\Entity\Type
     */
    public function getEntityType()
    {
        return $this->_getResource()->getEntityType();
    }

    /**
     * Return Entity Type ID
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        $entityTypeId = $this->getData('entity_type_id');
        if (!$entityTypeId) {
            $entityTypeId = $this->getEntityType()->getId();
            $this->setData('entity_type_id', $entityTypeId);
        }
        return $entityTypeId;
    }

    /**
     * Return Region ID
     *
     * @return int
     */
    public function getRegionId()
    {
        return (int) $this->getData('region_id');
    }

    /**
     * Set Region ID. $regionId is automatically converted to integer
     *
     * @param int $regionId
     * @return \Magento\Customer\Model\Address
     */
    public function setRegionId($regionId)
    {
        $this->setData('region_id', (int) $regionId);
        return $this;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function _createCustomer()
    {
        return $this->_customerFactory->create();
    }
}
