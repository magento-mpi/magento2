<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Model\System\Config\Source\Worldpay;

class AccountType
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'business',
                'label' => __('Business'),
            ],
            [
                'value' => 'corporate',
                'label' => __('Corporate')
            ]
        ];
    }
}
