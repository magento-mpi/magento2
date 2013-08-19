<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for available logo types
 */
class Magento_Paypal_Model_System_Config_Source_Logo
{
    public function toOptionArray()
    {
        $result = array('' => __('No Logo'));
        $result += Mage::getModel('Magento_Paypal_Model_Config')->getAdditionalOptionsLogoTypes();
        return $result;
    }
}
