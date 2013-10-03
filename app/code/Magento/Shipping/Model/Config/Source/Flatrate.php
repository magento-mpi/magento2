<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Shipping\Model\Config\Source;

class Flatrate implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=> __('None')),
            array('value'=>'O', 'label'=>__('Per Order')),
            array('value'=>'I', 'label'=>__('Per Item')),
        );
    }
}
