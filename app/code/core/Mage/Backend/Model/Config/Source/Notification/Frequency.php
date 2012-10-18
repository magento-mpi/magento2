<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * AdminNotification update frequency source
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Source_Notification_Frequency
{
    public function toOptionArray()
    {
        return array(
            1   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('1 Hour'),
            2   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('2 Hours'),
            6   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('6 Hours'),
            12  => Mage::helper('Mage_Adminhtml_Helper_Data')->__('12 Hours'),
            24  => Mage::helper('Mage_Adminhtml_Helper_Data')->__('24 Hours')
        );
    }
}
