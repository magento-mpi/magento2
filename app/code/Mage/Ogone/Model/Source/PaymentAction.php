<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ogone Payment Action Dropdown source
 */
class Mage_Ogone_Model_Source_PaymentAction
{
    /**
     * Prepare payment action list as optional array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '', 'label' => Mage::helper('Mage_Ogone_Helper_Data')->__('Ogone Default Operation')),
            array('value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE, 'label' => Mage::helper('Mage_Ogone_Helper_Data')->__('Authorization')),
            array('value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE, 'label' => Mage::helper('Mage_Ogone_Helper_Data')->__('Direct Sale')),
        );
    }
}