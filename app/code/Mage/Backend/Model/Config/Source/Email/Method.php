<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Source for email send method
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Source_Email_Method implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $options    = array(
            array(
                'value' => 'bcc',
                'label' => Mage::helper('Mage_Backend_Helper_Data')->__('Bcc')
            ),
            array(
                'value' => 'copy',
                'label' => Mage::helper('Mage_Backend_Helper_Data')->__('Separate Email')
            ),
        );
        return $options;
    }
}
