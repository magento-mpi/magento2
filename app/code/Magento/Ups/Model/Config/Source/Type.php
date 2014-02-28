<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ups\Model\Config\Source;

use Magento\Data\OptionSourceInterface;

/**
 * Class Type
 */
class Type implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'UPS', 'label' => __('United Parcel Service')),
            array('value' => 'UPS_XML', 'label' => __('United Parcel Service XML')),
        );
    }
}
