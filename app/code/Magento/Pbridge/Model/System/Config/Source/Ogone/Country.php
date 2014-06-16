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
        return array(
            array(
                'value' => 'AT',
                'label' => __('Austria')
            ),
            array(
                'value' => 'DE',
                'label' => __('Germany')
            ),
            array(
                'value' => 'NL',
                'label' => __('Netherlands')
            ),
        );
    }
}
