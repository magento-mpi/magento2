<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Model\System\Config\Source\Payone;

use Magento\Payment\Model\Method\AbstractMethod;

class PaymentAction
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => AbstractMethod::ACTION_AUTHORIZE,
                'label' => __('Preauthorization')
            ),
            array(
                'value' => AbstractMethod::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorization')
            ),
        );
    }
}
