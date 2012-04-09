<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Permission source model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Acl_Global_Rule_Permission
{
    /**#@+
     * Source keys
     */
    const TYPE_ALLOW = 1;
    const TYPE_DENY  = 0;
    /**#@-*/

    /**
     * Get options parameters
     *
     * @return array
     */
    static public function toOptionArray()
    {
        return array(
            array(
                'value' => self::TYPE_DENY,
                'label' => Mage::helper('Mage_Api2_Helper_Data')->__('Deny')
            ),
            array(
                'value' => self::TYPE_ALLOW,
                'label' => Mage::helper('Mage_Api2_Helper_Data')->__('Allow')
            ),
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
            self::TYPE_DENY  => Mage::helper('Mage_Api2_Helper_Data')->__('Deny'),
            self::TYPE_ALLOW => Mage::helper('Mage_Api2_Helper_Data')->__('Allow'),
        );
    }
}
