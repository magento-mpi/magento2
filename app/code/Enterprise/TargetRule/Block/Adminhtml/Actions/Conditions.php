<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Enterprise_TargetRule_Block_Adminhtml_Actions_Conditions implements Magento_Data_Form_Element_Renderer_Interface
{
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        if ($element->getRule() && $element->getRule()->getActions()) {
            return $element->getRule()->getActions()->asHtmlRecursive();
        }
        return '';
    }
}

