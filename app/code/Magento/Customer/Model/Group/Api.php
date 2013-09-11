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
 * Customer groups api
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Group;

class Api extends \Magento\Api\Model\Resource\AbstractResource
{
    /**
     * Retrieve groups
     *
     * @return array
     */
    public function items()
    {
        $collection = \Mage::getModel('Magento\Customer\Model\Group')->getCollection();

        $result = array();
        foreach ($collection as $group) {
            /* @var $group \Magento\Customer\Model\Group */
            $result[] = $group->toArray(array('customer_group_id', 'customer_group_code'));
        }

        return $result;
    }
}
