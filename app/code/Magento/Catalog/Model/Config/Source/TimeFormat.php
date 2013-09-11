<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Config\Source;

class TimeFormat implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => '12h', 'label' => __('12h AM/PM')),
            array('value' => '24h', 'label' => __('24h')),
        );
    }
}
