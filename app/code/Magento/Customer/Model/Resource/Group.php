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
 * Customer group resource model
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Resource_Group extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Customer data
     *
     * @var Magento_Customer_Helper_Data
     */
    protected $_customerData = null;

    /**
     * @var Magento_Customer_Model_Resource_Customer_CollectionFactory
     */
    protected $_customersFactory;

    /**
     * Class constructor
     *
     * @param Magento_Customer_Helper_Data $customerData
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Customer_Model_Resource_Customer_CollectionFactory $customersFactory
     */
    public function __construct(
        Magento_Customer_Helper_Data $customerData,
        Magento_Core_Model_Resource $resource,
        Magento_Customer_Model_Resource_Customer_CollectionFactory $customersFactory
    ) {
        $this->_customerData = $customerData;
        $this->_customersFactory = $customersFactory;
        parent::__construct($resource);
    }

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('customer_group', 'customer_group_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Magento_Customer_Model_Resource_Group
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(
            array(
                'field' => 'customer_group_code',
                'title' => __('Customer Group')
            ));

        return $this;
    }

    /**
     * Check if group uses as default
     *
     * @param  Magento_Core_Model_Abstract $group
     * @throws Magento_Core_Exception
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeDelete(Magento_Core_Model_Abstract $group)
    {
        if ($group->usesAsDefault()) {
            throw new Magento_Core_Exception(__('The group "%1" cannot be deleted', $group->getCode()));
        }
        return parent::_beforeDelete($group);
    }

    /**
     * Method set default group id to the customers collection
     *
     * @param Magento_Core_Model_Abstract $group
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _afterDelete(Magento_Core_Model_Abstract $group)
    {
        $customerCollection = $this->_createCustomersCollection()
            ->addAttributeToFilter('group_id', $group->getId())
            ->load();
        foreach ($customerCollection as $customer) {
            $customer->load();
            $defaultGroupId = $this->_customerData->getDefaultCustomerGroupId($customer->getStoreId());
            $customer->setGroupId($defaultGroupId);
            $customer->save();
        }
        return parent::_afterDelete($group);
    }

    /**
     * @return Magento_Customer_Model_Resource_Customer_Collection
     */
    protected function _createCustomersCollection()
    {
        return $this->_customersFactory->create();
    }
}
