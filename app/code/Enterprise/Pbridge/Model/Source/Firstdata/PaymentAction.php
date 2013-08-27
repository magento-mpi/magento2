<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Firstdata Payment Action Dropdown source
 *
 * @category    Magento
 * @package     Enterprise_Pbridge
 * @author      Magento
 */
class Enterprise_Pbridge_Model_Source_Firstdata_PaymentAction
{
    /**
     * Return list of available payment actions for gateway
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE,
                'label' => __('Authorize Only')
            ),
            array(
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize and Capture')
            ),
        );
    }
}
