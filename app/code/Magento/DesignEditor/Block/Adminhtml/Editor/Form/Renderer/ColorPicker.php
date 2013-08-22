<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Color-picker form element renderer
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_ColorPicker
    extends Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Recursive
{
    /**
     * Set of templates to render
     *
     * Upper is rendered first and is inserted into next using <?php echo $this->getHtml() ?>
     * Templates used are based fieldset/element.phtml but split into several templates
     *
     * @var array
     */
    protected $_templates = array(
        'Magento_DesignEditor::editor/form/renderer/element/input.phtml',
        'Magento_DesignEditor::editor/form/renderer/color-picker.phtml',
        'Magento_DesignEditor::editor/form/renderer/element/wrapper.phtml',
        'Magento_DesignEditor::editor/form/renderer/simple.phtml'
    );

    /**
     * Get HTMl class of a field
     *
     * Actually it will be added to a field wrapper
     *
     * @return array
     */
    public function getFieldClass()
    {
        /** @var $element Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker */
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

    /**
     * Get field attributes string
     *
     * Actually it will be added to a field wrapper
     *
     * @see Magento_DesignEditor::editor/form/renderer/simple.phtml
     * @return string
     */
    public function getFieldAttributes()
    {
        $element = $this->getElement();

        $fieldAttributes = array();
        if ($element->getHtmlContainerId()) {
            $fieldAttributes[] = sprintf('id="%s"', $element->getHtmlContainerId());
        }
        $fieldAttributes[] = sprintf('class="%s"', join(' ', $this->getFieldClass()));
        $fieldAttributes[] = $this->getUiId('form-field', $element->getId());

        return join(' ', $fieldAttributes);
    }
}
