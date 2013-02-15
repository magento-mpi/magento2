<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Column element renderer
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Column_Element
    extends Mage_Backend_Block_Widget_Form_Renderer_Fieldset_Element
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if ($element instanceof Varien_Data_Form_Element_Fieldset) {
            $element->setLegend($element->getLabel());
            $this->setTemplate('Mage_Backend::widget/form/renderer/fieldset.phtml');
        } else {
            $this->setTemplate('Mage_Backend::widget/form/renderer/fieldset/element.phtml');
        }

        return parent::render($element);
    }
}
