<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Rule\Block;

class Newchild extends \Magento\Core\Block\AbstractBlock
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
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
