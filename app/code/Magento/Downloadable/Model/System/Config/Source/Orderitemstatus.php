<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Model\System\Config\Source;

/**
 * Downloadable Order Item Status Source
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Orderitemstatus implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => \Magento\Sales\Model\Order\Item::STATUS_PENDING,
                'label' => __('Pending')
            ),
            array(
                'value' => \Magento\Sales\Model\Order\Item::STATUS_INVOICED,
                'label' => __('Invoiced')
            )
        );
    }
}
