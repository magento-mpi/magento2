<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\GoogleCheckout\Model\Source\Shipping;

class Units implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'IN', 'label' => __('Inches')),
        );
    }
}
