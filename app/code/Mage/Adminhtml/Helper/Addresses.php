<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml addresses helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Addresses extends Mage_Core_Helper_Abstract
{
    const DEFAULT_STREET_LINES_COUNT = 2;

    /**
     * Check if number of street lines is non-zero
     *
     * @param Mage_Customer_Model_Attribute $attribute
     * @return Mage_Customer_Model_Attribute
     */
    public function processStreetAttribute(Mage_Customer_Model_Attribute $attribute)
    {
        if($attribute->getScopeMultilineCount() <= 0) {
            $attribute->setScopeMultilineCount(self::DEFAULT_STREET_LINES_COUNT);
        }
        return $attribute;
    }
}
