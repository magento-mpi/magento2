<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward Customer Groups source model
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Source\Customer;

class Groups implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Customer\Model\Resource\Group\CollectionFactory
     */
    protected $_groupsFactory;

    /**
     * @param \Magento\Customer\Model\Resource\Group\CollectionFactory $groupsFactory
     */
    public function __construct(\Magento\Customer\Model\Resource\Group\CollectionFactory $groupsFactory)
    {
        $this->_groupsFactory = $groupsFactory;
    }

    /**
     * Retrieve option array of customer groups
     *
     * @return array
     */
    public function toOptionArray()
    {
        $groups = $this->_groupsFactory->create()
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();
        $groups = array(0 => __('All Customer Groups'))
                + $groups;
        return $groups;
    }
}
