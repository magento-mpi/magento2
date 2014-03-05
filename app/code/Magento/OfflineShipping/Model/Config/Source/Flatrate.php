<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\OfflineShipping\Model\Config\Source;

class Flatrate implements \Magento\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=> __('None')),
            array('value'=>'O', 'label'=>__('Per Order')),
            array('value'=>'I', 'label'=>__('Per Item')),
        );
    }
}
