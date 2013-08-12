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
 * Source model for Require Billing Address
 */
class Magento_Paypal_Model_System_Config_Source_RequireBillingAddress
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var $configModel Magento_Paypal_Model_Config */
        $configModel = Mage::getModel('Magento_Paypal_Model_Config');
        return $configModel->getRequireBillingAddressOptions();
    }
}
