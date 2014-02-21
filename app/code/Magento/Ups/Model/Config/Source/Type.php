<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ups\Model\Config\Source;

/**
 * Class Type
 */
class Type implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'UPS', 'label' => __('United Parcel Service')),
            #array('value' => \Magento\Paypal\Model\Api\AbstractApi::PAYMENT_TYPE_ORDER, 'label' => __('Order')),
            array('value' => 'UPS_XML', 'label' => __('United Parcel Service XML')),
        );
    }
}
