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
 * Source for email send method
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Source_Email_Method
{
    public function toOptionArray()
    {
        $options    = array(
            array(
                'value' => 'bcc',
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Bcc')
            ),
            array(
                'value' => 'copy',
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Separate Email')
            ),
        );
        return $options;
    }
}
