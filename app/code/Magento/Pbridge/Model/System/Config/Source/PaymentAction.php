<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Pbridge\Model\System\Config\Source;

use Magento\Payment\Model\Method\AbstractMethod;

class PaymentAction
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => AbstractMethod::ACTION_AUTHORIZE,
                'label' => __('Authorize Only'),
            ],
            [
                'value' => AbstractMethod::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize and Capture')
            ],
        ];
    }
}
