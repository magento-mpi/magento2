<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Api2
 */

/**
 * Privilege of rule source model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Acl_Global_Rule_Privilege
{
    /**
     * Get options parameters
     *
     * @return array
     */
    static public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Api2_Model_Resource::OPERATION_CREATE,
                'label' => Mage::helper('Mage_Api2_Helper_Data')->__('Create')
            ),
            array(
                'value' => Mage_Api2_Model_Resource::OPERATION_RETRIEVE,
                'label' => Mage::helper('Mage_Api2_Helper_Data')->__('Retrieve')
            ),
            array(
                'value' => Mage_Api2_Model_Resource::OPERATION_UPDATE,
                'label' => Mage::helper('Mage_Api2_Helper_Data')->__('Update')
            ),
            array(
                'value' => Mage_Api2_Model_Resource::OPERATION_DELETE,
                'label' => Mage::helper('Mage_Api2_Helper_Data')->__('Delete')
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
            Mage_Api2_Model_Resource::OPERATION_CREATE   => Mage::helper('Mage_Api2_Helper_Data')->__('Create'),
            Mage_Api2_Model_Resource::OPERATION_RETRIEVE => Mage::helper('Mage_Api2_Helper_Data')->__('Retrieve'),
            Mage_Api2_Model_Resource::OPERATION_UPDATE   => Mage::helper('Mage_Api2_Helper_Data')->__('Update'),
            Mage_Api2_Model_Resource::OPERATION_DELETE   => Mage::helper('Mage_Api2_Helper_Data')->__('Delete')
        );
    }
}
