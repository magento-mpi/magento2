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
namespace Magento\SalesRule\Model\System\Config\Source\Coupon;

class Format
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $formatsList = \Mage::helper('Magento\SalesRule\Helper\Coupon')->getFormatsList();
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
