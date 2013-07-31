<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders statuses option array
 *
 * @category   Mage
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Grid_StatusesArray implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Return option array
     * @return array
     */
    public function toOptionArray()
    {
        $statuses = Mage::getResourceModel('Mage_Sales_Model_Resource_Order_Status_Collection')
            ->toOptionHash();
        return $statuses;
    }
}
