<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model\Source;

class Status extends \Magento\Model\AbstractModel implements \Magento\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => \Magento\Sales\Model\Order\Item::STATUS_PENDING, 'label' => __('Ordered')),
            array('value' => \Magento\Sales\Model\Order\Item::STATUS_INVOICED, 'label' => __('Invoiced'))
        );
    }
}
