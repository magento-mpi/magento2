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
 * Cybersource Payment Action Dropdown source
 */
namespace Magento\Pbridge\Model\Source\Cybersource;

class PaymentAction
{
    public function toOptionArray()
    {
        return array(
            array('value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE, 'label' => __('Authorization')),
            array('value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE, 'label' => __('Sale')),
        );
    }
}
