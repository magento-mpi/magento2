<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for Authentication methods
 */
class Saas_Paypal_Model_System_Config_Source_AuthenticationMethod implements Mage_Core_Model_Option_ArrayInterface
{
    const TYPE_API_CREDENTIALS = 0;
    const TYPE_PERMISSIONS     = 1;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::TYPE_API_CREDENTIALS,
                'label' => Mage::helper('Saas_Paypal_Helper_Data')->__('API Credentials')),
            array('value' => self::TYPE_PERMISSIONS,
                'label' => Mage::helper('Saas_Paypal_Helper_Data')->__('Permissions')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            self::TYPE_API_CREDENTIALS => Mage::helper('Saas_Paypal_Helper_Data')->__('API Credentials'),
            self::TYPE_PERMISSIONS => Mage::helper('Saas_Paypal_Helper_Data')->__('Permissions'),
        );
    }
}
