<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Quote\Address\Attribute\Frontend;

/**
 * Quote address attribute frontend discount resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Discount extends \Magento\Sales\Model\Resource\Quote\Address\Attribute\Frontend
{
    /**
     * Fetch discount
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return $this
     */
    public function fetchTotals(\Magento\Sales\Model\Quote\Address $address)
    {
        $amount = $address->getDiscountAmount();
        if ($amount != 0) {
            $title = __('Discount');
            $couponCode = $address->getQuote()->getCouponCode();
            if (strlen($couponCode)) {
                $title .= sprintf(' (%s)', $couponCode);
            }
            $address->addTotal(array('code' => 'discount', 'title' => $title, 'value' => -$amount));
        }
        return $this;
    }
}
