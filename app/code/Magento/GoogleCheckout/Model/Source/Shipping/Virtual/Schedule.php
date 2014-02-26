<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\GoogleCheckout\Model\Source\Shipping\Virtual;

class Schedule implements \Magento\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'OPTIMISTIC',  'label' => __('Optimistic')),
            array('value' => 'PESSIMISTIC', 'label' => __('Pessimistic')),
        );
    }
}
