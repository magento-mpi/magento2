<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Model\Config\Source\Cart;

class Summary implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => __('Display number of items in cart')),
            array('value' => 1, 'label' => __('Display item quantities'))
        );
    }
}
