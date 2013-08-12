<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Enterprise_TargetRule_Block_Adminhtml_Rule_Conditions implements Magento_Data_Form_Element_Renderer_Interface
{
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        if ($element->getRule() && $element->getRule()->getConditions()) {
            return $element->getRule()->getConditions()->asHtmlRecursive();
        }
        return '';
    }
}
