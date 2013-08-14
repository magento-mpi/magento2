<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Payone
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Payone Payment Action Dropdown source
 *
 * @category    Magento
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
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE,
                'label' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Authorize Only')
            ),
            array(
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('Enterprise_Pbridge_Helper_Data')->__('Authorize and Capture')
            ),
        );
    }
}
