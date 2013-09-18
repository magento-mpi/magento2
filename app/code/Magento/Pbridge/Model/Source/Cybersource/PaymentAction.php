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
class Magento_Pbridge_Model_Source_Cybersource_PaymentAction implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE, 'label' => __('Authorization')),
            array('value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE, 'label' => __('Sale')),
        );
    }
}
