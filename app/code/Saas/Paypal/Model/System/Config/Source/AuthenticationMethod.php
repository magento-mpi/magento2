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
class Saas_Paypal_Model_System_Config_Source_AuthenticationMethod
{
    const TYPE_API_CREDENTIALS = '0';
    const TYPE_PERMISSIONS     = '1';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            self::TYPE_API_CREDENTIALS => Mage::helper('Saas_Paypal_Helper_Data')->__('API Credentials'),
            self::TYPE_PERMISSIONS     => Mage::helper('Saas_Paypal_Helper_Data')->__('Permissions')
        );
    }
}
