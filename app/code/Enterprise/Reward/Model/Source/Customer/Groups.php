<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward Customer Groups source model
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Source_Customer_Groups implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve option array of customer groups
     *
     * @return array
     */
    public function toOptionArray()
    {
        $groups = Mage::getResourceModel('Magento_Customer_Model_Resource_Group_Collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();
        $groups = array(0 => __('All Customer Groups'))
                + $groups;
        return $groups;
    }
}
