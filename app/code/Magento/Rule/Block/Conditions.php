<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Rule_Block_Conditions implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        if ($element->getRule() && $element->getRule()->getConditions()) {
           return $element->getRule()->getConditions()->asHtmlRecursive();
        }
        return '';
    }
}
