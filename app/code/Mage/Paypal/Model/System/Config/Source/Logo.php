<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for available logo types
 */
class Mage_Paypal_Model_System_Config_Source_Logo
{
    public function toOptionArray()
    {
        $result = array('' => __('No Logo'));
        $result += Mage::getModel('Mage_Paypal_Model_Config')->getAdditionalOptionsLogoTypes();
        return $result;
    }
}
