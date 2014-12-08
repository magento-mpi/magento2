<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Model\System\Config\Source\Ogone;

class Country
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'AT',
                'label' => __('Austria'),
            ],
            [
                'value' => 'DE',
                'label' => __('Germany')
            ],
            [
                'value' => 'NL',
                'label' => __('Netherlands')
            ],
        ];
    }
}
