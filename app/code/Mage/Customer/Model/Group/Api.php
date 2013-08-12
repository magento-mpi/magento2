<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer groups api
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Group_Api extends Magento_Api_Model_Resource_Abstract
{
    /**
     * Retrieve groups
     *
     * @return array
     */
    public function items()
    {
        $collection = Mage::getModel('Mage_Customer_Model_Group')->getCollection();

        $result = array();
        foreach ($collection as $group) {
            /* @var $group Mage_Customer_Model_Group */
            $result[] = $group->toArray(array('customer_group_id', 'customer_group_code'));
        }

        return $result;
    }
}
