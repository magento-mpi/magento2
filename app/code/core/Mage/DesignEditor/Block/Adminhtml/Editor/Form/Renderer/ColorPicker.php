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
 * Color-picker form element renderer
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_ColorPicker
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Recursive
{
    //former fieldset/element.phtml but split into several templates
    protected $_templates = array(
        'Mage_DesignEditor::editor/form/renderer/element/input.phtml',
        'Mage_DesignEditor::editor/form/renderer/color-picker.phtml',
        'Mage_DesignEditor::editor/form/renderer/element/wrapper.phtml',
        'Mage_DesignEditor::editor/form/renderer/simple.phtml'
    );

    //used here
    public function getFieldClass()
    {
        $element = $this->getElement();

        $elementBeforeLabel = $element->getExtType() == 'checkbox' || $element->getExtType() == 'radio';
        $addOn = $element->getBeforeElementHtml() || $element->getAfterElementHtml();

        //@TODO add class that show the control type 'color-picker' for this one
        $classes = array();
        $classes[] = 'field';
        $classes[] = 'field-' . $element->getId();
        $classes[] = $element->getCssClass();
        if ($elementBeforeLabel) {
            $classes[] = 'choice';
        }
        if ($addOn) {
            $classes[] = 'with-addon';
        }
        if ($element->getRequired()) {
            $classes[] = 'required';
        }
        if ($element->getNote()) {
            $classes[] = 'with-note';
        }

        return $classes;
    }

    //used in Mage_DesignEditor::editor/form/renderer/simple.phtml
    public function getFieldAttributes()
    {
        $element = $this->getElement();

        $fieldId = ($element->getHtmlContainerId()) ? ' id="' . $element->getHtmlContainerId() . '"' : '';

        $fieldAttributes = $fieldId . ' class="' . join(' ', $this->getFieldClass()) . '" '
            . $this->getUiId('form-field', $element->getId());

        return $fieldAttributes;
    }
}
