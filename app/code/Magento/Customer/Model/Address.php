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
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method int getParentId() getParentId()
 * @method Magento_Customer_Model_Address setParentId() setParentId(int $parentId)
 */
class Magento_Customer_Model_Address extends Magento_Customer_Model_Address_Abstract
{
    /**
     * Customer entity
     *
     * @var Magento_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Directory_Helper_Data $directoryData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Customer_Model_Address_Config $addressConfig
     * @param Magento_Directory_Model_RegionFactory $regionFactory
     * @param Magento_Directory_Model_CountryFactory $countryFactory
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Directory_Helper_Data $directoryData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Customer_Model_Address_Config $addressConfig,
        Magento_Directory_Model_RegionFactory $regionFactory,
        Magento_Directory_Model_CountryFactory $countryFactory,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
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
        $this->_init('Magento_Customer_Model_Resource_Address');
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
     * @return Magento_Customer_Model_Address
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
     * @return Magento_Customer_Model_Customer|bool
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
     * @param Magento_Customer_Model_Customer $customer
     * @return Magento_Customer_Model_Address
     */
    public function setCustomer(Magento_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        $this->setCustomerId($customer->getId());
        return $this;
    }

    /**
     * Delete customer address
     *
     * @return Magento_Customer_Model_Address
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
     * @return Magento_Eav_Model_Entity_Type
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
     * @return Magento_Customer_Model_Address
     */
    public function setRegionId($regionId)
    {
        $this->setData('region_id', (int) $regionId);
        return $this;
    }

    /**
     * @return Magento_Customer_Model_Customer
     */
    protected function _createCustomer()
    {
        return $this->_customerFactory->create();
    }
}
