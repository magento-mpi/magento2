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
 * Quote address attribute frontend subtotal resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Subtotal extends \Magento\Sales\Model\Resource\Quote\Address\Attribute\Frontend
{
    /**
     * Add total
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return $this
     */
    public function fetchTotals(\Magento\Sales\Model\Quote\Address $address)
    {
        $address->addTotal(array('code' => 'subtotal', 'title' => __('Subtotal'), 'value' => $address->getSubtotal()));

        return $this;
    }
}
