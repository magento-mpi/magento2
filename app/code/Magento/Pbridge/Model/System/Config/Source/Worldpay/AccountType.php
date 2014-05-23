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
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'business',
                'label' => __('Business')
            ),
            array(
                'value' => 'corporate',
                'label' => __('Corporate')
            )
        );
    }
}
