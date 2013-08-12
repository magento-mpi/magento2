<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fielset renderer which expanded by default
 */
class Mage_Paypal_Block_Adminhtml_System_Config_Fieldset_Expanded
    extends Mage_Backend_Block_System_Config_Form_Fieldset
{
    /**
     * Return collapse state
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _isCollapseState($element)
    {
        $extra = Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->getExtra();
        if (isset($extra['configState'][$element->getId()])) {
            return $extra['configState'][$element->getId()];
        }

        return true;
    }
}
