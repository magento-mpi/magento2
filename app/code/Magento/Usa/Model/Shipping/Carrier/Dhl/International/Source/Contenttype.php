<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Usa\Model\Shipping\Carrier\Dhl\International\Source;

/**
 * Source model for DHL Content Type
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Contenttype implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            array('label' => __('Documents'),
                'value' => \Magento\Usa\Model\Shipping\Carrier\Dhl\International::DHL_CONTENT_TYPE_DOC),
            array('label' => __('Non documents'),
                'value' => \Magento\Usa\Model\Shipping\Carrier\Dhl\International::DHL_CONTENT_TYPE_NON_DOC),
        );
    }
}
