<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Model\System\Config\Source;

/**
 * Downloadable Order Item Status Source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Orderitemstatus implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => \Magento\Sales\Model\Order\Item::STATUS_PENDING, 'label' => __('Pending')),
            array('value' => \Magento\Sales\Model\Order\Item::STATUS_INVOICED, 'label' => __('Invoiced'))
        );
    }
}
