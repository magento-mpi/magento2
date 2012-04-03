<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Api2
 */

/**
 * Operation source model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Acl_Filter_Attribute_Operation
{
    /**
     * Get options paramets
     *
     * @return array
     */
    static public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ,
                'label' => Mage::helper('Mage_Api2_Helper_Data')->__('Read')
            ),
            array(
                'value' => Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_WRITE,
                'label' => Mage::helper('Mage_Api2_Helper_Data')->__('Write')
            )
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    static public function toArray()
    {
        return array(
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ  => Mage::helper('Mage_Api2_Helper_Data')->__('Read'),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_WRITE => Mage::helper('Mage_Api2_Helper_Data')->__('Write')
        );
    }
}
