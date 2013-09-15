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
     * Sales rule coupon
     *
     * @var Magento_SalesRule_Helper_Coupon
     */
    protected $_salesRuleCoupon = null;

    /**
     * @param Magento_SalesRule_Helper_Coupon $salesRuleCoupon
     */
    public function __construct(
        Magento_SalesRule_Helper_Coupon $salesRuleCoupon
    ) {
        $this->_salesRuleCoupon = $salesRuleCoupon;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $formatsList = $this->_salesRuleCoupon->getFormatsList();
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
