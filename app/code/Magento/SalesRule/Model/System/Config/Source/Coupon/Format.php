<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\System\Config\Source\Coupon;

/**
 * Options for Code Format Field in Auto Generated Specific Coupon Codes configuration section
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Format implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Sales rule coupon
     *
     * @var \Magento\SalesRule\Helper\Coupon
     */
    protected $_salesRuleCoupon = null;

    /**
     * @param \Magento\SalesRule\Helper\Coupon $salesRuleCoupon
     */
    public function __construct(\Magento\SalesRule\Helper\Coupon $salesRuleCoupon)
    {
        $this->_salesRuleCoupon = $salesRuleCoupon;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $formatsList = $this->_salesRuleCoupon->getFormatsList();
        $result = array();
        foreach ($formatsList as $formatId => $formatTitle) {
            $result[] = array('value' => $formatId, 'label' => $formatTitle);
        }

        return $result;
    }
}
