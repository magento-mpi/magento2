<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
