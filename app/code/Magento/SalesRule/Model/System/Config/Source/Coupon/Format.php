<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Options for Code Format Field in Auto Generated Specific Coupon Codes configuration section
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_SalesRule_Model_System_Config_Source_Coupon_Format
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $formatsList = Mage::helper('Magento_SalesRule_Helper_Coupon')->getFormatsList();
        $result = array();
        foreach ($formatsList as $formatId => $formatTitle) {
            $result[] = array(
                'value' => $formatId,
                'label' => $formatTitle
            );
        }

        return $result;
    }
}