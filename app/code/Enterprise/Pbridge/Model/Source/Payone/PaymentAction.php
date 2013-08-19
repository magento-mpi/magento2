<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payone
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Payone Payment Action Dropdown source
 *
 * @category    Mage
 * @package     Mage_Payone
 * @author      Magento
 */
class Enterprise_Pbridge_Model_Source_Payone_PaymentAction
{
    /**
     * Return list of available payment actions for gateway
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE,
                'label' => __('Authorize Only')
            ),
            array(
                'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize and Capture')
            ),
        );
    }
}
