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
 * Source model for available Authorization Amounts for Account Verification
 *
 * @deprecated since 1.6.2.0
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Model_System_Config_Source_AuthorizationAmounts
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array();
    }
}
