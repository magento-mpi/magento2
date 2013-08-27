<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml addresses helper
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Helper_Addresses extends Magento_Core_Helper_Abstract
{
    const DEFAULT_STREET_LINES_COUNT = 2;

    /**
     * Check if number of street lines is non-zero
     *
     * @param Magento_Customer_Model_Attribute $attribute
     * @return Magento_Customer_Model_Attribute
     */
    public function processStreetAttribute(Magento_Customer_Model_Attribute $attribute)
    {
        if($attribute->getScopeMultilineCount() <= 0) {
            $attribute->setScopeMultilineCount(self::DEFAULT_STREET_LINES_COUNT);
        }
        return $attribute;
    }
}
