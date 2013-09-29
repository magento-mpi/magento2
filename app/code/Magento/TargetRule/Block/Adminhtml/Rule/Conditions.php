<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\TargetRule\Block\Adminhtml\Rule;

class Conditions implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        if ($element->getRule() && $element->getRule()->getConditions()) {
            return $element->getRule()->getConditions()->asHtmlRecursive();
        }
        return '';
    }
}
