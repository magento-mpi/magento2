<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for DHL Content Type
 *
 * @category   Magento
 * @package    Magento_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Usa\Model\Shipping\Carrier\Dhl\International\Source;

class Contenttype implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
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
