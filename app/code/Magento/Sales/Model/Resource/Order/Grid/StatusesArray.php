<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders statuses option array
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Grid_StatusesArray implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Return option array
     * @return array
     */
    public function toOptionArray()
    {
        $statuses = Mage::getResourceModel('Magento_Sales_Model_Resource_Order_Status_Collection')
            ->toOptionHash();
        return $statuses;
    }
}
