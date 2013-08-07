<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Rule_Model_Renderer_Actions implements Magento_Data_Form_Element_Renderer_Interface
{
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        if ($element->getRule() && $element->getRule()->getActions()) {
           return $element->getRule()->getActions()->asHtmlRecursive();
        }
        return '';
    }
}
