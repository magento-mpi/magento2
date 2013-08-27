<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Braintree Payment Action Dropdown source
 */
class Enterprise_Pbridge_Model_Source_Braintree_PaymentAction
{
    /**
     * Return list of available payment actions for gateway
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE,
                'label' => __('Authorization')),
            array('value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Sale')),
        );
    }
}

