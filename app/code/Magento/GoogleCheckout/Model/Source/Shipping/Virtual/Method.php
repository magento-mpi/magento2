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

class Method
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'email', 'label' => __('Email delivery')),
            // array('value'=>'key_url', 'label'=> __('Key/URL delivery')),
            // array('value'=>'description_based', 'label'=> __('Description-based delivery'))
        );
    }
}
