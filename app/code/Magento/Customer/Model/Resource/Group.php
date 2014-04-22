<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Resource;

/**
 * Customer group resource model
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Group extends \Magento\Model\Resource\Db\AbstractDb
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
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Customer\Helper\Data $customerData
     * @param \Magento\Customer\Model\Resource\Customer\CollectionFactory $customersFactory
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Customer\Helper\Data $customerData,
        \Magento\Customer\Model\Resource\Customer\CollectionFactory $customersFactory
    ) {
        $this->_customerData = $customerData;
        $this->_customersFactory = $customersFactory;
        parent::__construct($resource);
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customer_group', 'customer_group_id');
    }

    /**
     * Initialize unique fields
     *
     * @return $this
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array('field' => 'customer_group_code', 'title' => __('Customer Group')));

        return $this;
    }

    /**
     * Check if group uses as default
     *
     * @param  \Magento\Model\AbstractModel $group
     * @return $this
     * @throws \Magento\Model\Exception
     */
    protected function _beforeDelete(\Magento\Model\AbstractModel $group)
    {
        if ($group->usesAsDefault()) {
            throw new \Magento\Model\Exception(__('The group "%1" cannot be deleted', $group->getCode()));
        }
        return parent::_beforeDelete($group);
    }

    /**
     * Method set default group id to the customers collection
     *
     * @param \Magento\Model\AbstractModel $group
     * @return $this
     */
    protected function _afterDelete(\Magento\Model\AbstractModel $group)
    {
        $customerCollection = $this->_createCustomersCollection()->addAttributeToFilter(
            'group_id',
            $group->getId()
        )->load();
        foreach ($customerCollection as $customer) {
            /** @var $customer \Magento\Customer\Model\Customer */
            $customer->load($customer->getId());
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
