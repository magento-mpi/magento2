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
class Magento_Reward_Model_Source_Customer_Groups implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Customer_Model_Resource_Group_CollectionFactory
     */
    protected $_groupCollFactory;

    /**
     * @param Magento_Customer_Model_Resource_Group_CollectionFactory $groupCollFactory
     */
    public function __construct(Magento_Customer_Model_Resource_Group_CollectionFactory $groupCollFactory)
    {
        $this->_groupCollFactory = $groupCollFactory;
    }

    /**
     * Retrieve option array of customer groups
     *
     * @return array
     */
    public function toOptionArray()
    {
        $groups = $this->_groupCollFactory
            ->create()
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();
        $groups = array(0 => __('All Customer Groups'))
                + $groups;
        return $groups;
    }
}
