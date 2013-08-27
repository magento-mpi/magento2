<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for Ogone DirectLink Payment Actions
 */
class Enterprise_Pbridge_Model_Source_Ogone_PaymentAction
{
    /**
     * Prepare payment action list as optional array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE, 'label' => __('Authorization')),
            array('value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE, 'label' => __('Direct Sale')),
        );
    }
}
