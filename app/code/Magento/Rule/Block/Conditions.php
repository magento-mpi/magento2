<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Rule_Block_Conditions implements Magento_Data_Form_Element_Renderer_Interface
{
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        if ($element->getRule() && $element->getRule()->getConditions()) {
           return $element->getRule()->getConditions()->asHtmlRecursive();
        }
        return '';
    }
}
