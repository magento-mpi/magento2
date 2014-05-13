<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\Source;

class TimeFormat implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '12h', 'label' => __('12h AM/PM')),
            array('value' => '24h', 'label' => __('24h'))
        );
    }
}
