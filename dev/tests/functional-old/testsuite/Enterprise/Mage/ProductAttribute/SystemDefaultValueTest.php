<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ProductAttribute
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Check the possibility to set default value to system attributes with dropdown type
 */
class Enterprise_Mage_ProductAttribute_SystemDefaultValueTest extends Core_Mage_ProductAttribute_SystemDefaultValueTest
{
    /**
     * DataProvider with system attributes list
     *
     * @return array
     */
    public function systemAttributeDataProvider()
    {
        return array(
            array('is_returnable', 'simple', 'general_enable_rma'),
            array('gift_wrapping_available', 'simple', 'autosettings_allow_gift_wrapping'),
            array('allow_open_amount', 'giftcard', 'general_allow_open_amount')
        );
    }
}
