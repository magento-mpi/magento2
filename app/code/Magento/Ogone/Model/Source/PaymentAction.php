<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ogone Payment Action Dropdown source
 */
namespace Magento\Ogone\Model\Source;

class PaymentAction
{
    /**
     * Prepare payment action list as optional array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '', 'label' => __('Ogone Default Operation')),
            array('value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE, 'label' => __('Authorization')),
            array('value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE, 'label' => __('Direct Sale')),
        );
    }
}
