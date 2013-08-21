<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category   Magento
 * @package    Magento_Cardgate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cardgate_Model_Gateway_Dummy extends Magento_Cardgate_Model_Gateway_Creditcard
{
    /**
     * Rewrite to make Dummy method always unavailable
     *
     * @param Magento_Sales_Model_Quote $quote
     * @return bool
     *
     * Suppress this rule as $order parameter is a part of method signature
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isAvailable($quote = null)
    {
        return false;
    }
}
