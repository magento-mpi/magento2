<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Rule_Block_Newchild extends Magento_Core_Block_Abstract
    implements Magento_Data_Form_Element_Renderer_Interface
{
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $element->addClass('element-value-changer');
        $html = '&nbsp;<span class="rule-param rule-param-new-child"' . ($element->getParamId() ? ' id="' . $element->getParamId() . '"' : '') . '>';
        $html.= '<a href="javascript:void(0)" class="label">';
        $html.= $element->getValueName();
        $html.= '</a><span class="element">';
        $html.= $element->getElementHtml();
        $html.= '</span></span>&nbsp;';
        return $html;
    }
}
