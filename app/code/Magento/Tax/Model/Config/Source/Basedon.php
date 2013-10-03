<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Config\Source;

class Basedon implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'shipping', 'label'=>__('Shipping Address')),
            array('value'=>'billing', 'label'=>__('Billing Address')),
            array('value'=>'origin', 'label'=>__("Shipping Origin")),
        );
    }

}
