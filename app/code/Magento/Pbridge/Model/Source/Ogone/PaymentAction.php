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
 * Source model for Ogone DirectLink Payment Actions
 */
namespace Magento\Pbridge\Model\Source\Ogone;

class PaymentAction implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Prepare payment action list as optional array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE, 'label' => __('Authorization')),
            array('value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE, 'label' => __('Direct Sale')),
        );
    }
}
