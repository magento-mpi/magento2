<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fielset renderer which expanded by default
 */
class Magento_Paypal_Block_Adminhtml_System_Config_Fieldset_Expanded
    extends Magento_Backend_Block_System_Config_Form_Fieldset
{
    /**
     * Return collapse state
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return bool
     */
    protected function _isCollapseState($element)
    {
        $extra = Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()->getExtra();
        if (isset($extra['configState'][$element->getId()])) {
            return $extra['configState'][$element->getId()];
        }

        return true;
    }
}
