<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment Action Dropdown source
 */
namespace Magento\Pbridge\Model\Source;

class PaymentAction implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return list of available payment actions for gateway
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE,
                'label' => __('Authorization')
            ),
            array(
                'value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Sale')
            )
        );
    }
}
