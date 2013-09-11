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

class Category
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'COMMERCIAL',  'label' => __('Commercial')),
            array('value' => 'RESIDENTIAL', 'label' => __('Residential')),
        );
    }
}
