<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Quote\Address\Attribute\Frontend;

/**
 * Quote address attribute frontend shipping resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shipping extends \Magento\Sales\Model\Resource\Quote\Address\Attribute\Frontend
{
    /**
     * Fetch totals
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return $this
     */
    public function fetchTotals(\Magento\Sales\Model\Quote\Address $address)
    {
        $amount = $address->getShippingAmount();
        if ($amount != 0) {
            $title = __('Shipping & Handling');
            if ($address->getShippingDescription()) {
                $title .= sprintf(' (%s)', $address->getShippingDescription());
            }
            $address->addTotal(
                array('code' => 'shipping', 'title' => $title, 'value' => $address->getShippingAmount())
            );
        }
        return $this;
    }
}
