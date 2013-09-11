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
            \Mage::throwException(__('The group "%1" cannot be deleted', $group->getCode()));
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
        $customerCollection = \Mage::getResourceModel('\Magento\Customer\Model\Resource\Customer\Collection')
            ->addAttributeToFilter('group_id', $group->getId())
            ->load();
        foreach ($customerCollection as $customer) {
            $customer->load();
            $defaultGroupId = \Mage::helper('Magento\Customer\Helper\Data')->getDefaultCustomerGroupId($customer->getStoreId());
            $customer->setGroupId($defaultGroupId);
            $customer->save();
        }
        return parent::_afterDelete($group);
    }
}
