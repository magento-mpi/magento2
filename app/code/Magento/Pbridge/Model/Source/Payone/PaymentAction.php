<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Payone Payment Action Dropdown source
 *
 * @author      Magento
 */
namespace Magento\Pbridge\Model\Source\Payone;

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
                'label' => __('Authorize Only')
            ),
            array(
                'value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize and Capture')
            )
        );
    }
}
