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
namespace Magento\Customer\Model\Resource;

class Group extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Customer data
     *
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerData = null;

    /**
     * @var \Magento\Customer\Model\Resource\Customer\CollectionFactory
     */
    protected $_customersFactory;

    /**
     * Class constructor
     *
     * @param \Magento\Customer\Helper\Data $customerData
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Customer\Model\Resource\Customer\CollectionFactory $customersFactory
     */
    public function __construct(
        \Magento\Customer\Helper\Data $customerData,
        \Magento\Core\Model\Resource $resource,
        \Magento\Customer\Model\Resource\Customer\CollectionFactory $customersFactory
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
     * @return \Magento\Customer\Model\Resource\Group
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
     * @param  \Magento\Core\Model\AbstractModel $group
     * @throws \Magento\Core\Exception
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected function _beforeDelete(\Magento\Core\Model\AbstractModel $group)
    {
        if ($group->usesAsDefault()) {
            throw new \Magento\Core\Exception(__('The group "%1" cannot be deleted', $group->getCode()));
        }
        return parent::_beforeDelete($group);
    }

    /**
     * Method set default group id to the customers collection
     *
     * @param \Magento\Core\Model\AbstractModel $group
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected function _afterDelete(\Magento\Core\Model\AbstractModel $group)
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
     * @return \Magento\Customer\Model\Resource\Customer\Collection
     */
    protected function _createCustomersCollection()
    {
        return $this->_customersFactory->create();
    }
}
