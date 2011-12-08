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
 * Source model for available Authorization Amounts for Account Verification
 */
class Mage_Paypal_Model_System_Config_Source_AuthorizationAmounts
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var $configModel Mage_Paypal_Model_Config */
        $configModel = Mage::getModel('Mage_Paypal_Model_Config');
        return $configModel->getAuthorizationAmounts();
    }
}
