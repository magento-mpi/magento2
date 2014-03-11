<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Dhl\Model\Source;

/**
 * Source model for DHL Content Type
 */
class Contenttype implements \Magento\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            array('label' => __('Documents'),
                'value' => \Magento\Dhl\Model\Carrier::DHL_CONTENT_TYPE_DOC),
            array('label' => __('Non documents'),
                'value' => \Magento\Dhl\Model\Carrier::DHL_CONTENT_TYPE_NON_DOC),
        );
    }
}
