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
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Uploader
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Recursive
{
    protected $_templates = array(
        'Mage_DesignEditor::editor/form/renderer/element/input.phtml',
        'Mage_DesignEditor::editor/form/renderer/uploader.phtml',
    );

    //used here
    public function getFieldClass()
    {
        $element = $this->getElement();

        $elementBeforeLabel = $element->getExtType() == 'checkbox' || $element->getExtType() == 'radio';
        $addOn = $element->getBeforeElementHtml() || $element->getAfterElementHtml();

        //@TODO add class that show the control type 'color-picker' for this one
        // mb use setExtType() ?
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

    /**
     * Get image upload url
     *
     * @return string
     */
    public function getImageUploadUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/uploadQuickStyleImage',
            array('theme_id' => Mage::registry('theme')->getId())
        );
    }

    /**
     * Get image upload url
     *
     * @return string
     */
    public function getImageRemoveUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/removeQuickStyleImage',
            array('theme_id' => Mage::registry('theme')->getId())
        );
    }
}
