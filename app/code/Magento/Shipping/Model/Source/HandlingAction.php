<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Model\Source;

class HandlingAction implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => \Magento\Shipping\Model\Carrier\AbstractCarrier::HANDLING_ACTION_PERORDER, 'label' => __('Per Order')),
            array('value' => \Magento\Shipping\Model\Carrier\AbstractCarrier::HANDLING_ACTION_PERPACKAGE , 'label' => __('Per Package')),
        );
    }
}
