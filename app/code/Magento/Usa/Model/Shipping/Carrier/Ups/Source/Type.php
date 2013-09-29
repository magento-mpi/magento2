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
 *
 * Usa Ups type action Dropdown source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Usa\Model\Shipping\Carrier\Ups\Source;

class Type implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'UPS', 'label' => __('United Parcel Service')),
            #array('value' => \Magento\Paypal\Model\Api\AbstractApi::PAYMENT_TYPE_ORDER, 'label' => __('Order')),
            array('value' => 'UPS_XML', 'label' => __('United Parcel Service XML')),
        );
    }
}
