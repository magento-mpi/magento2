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
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Font
    extends Mage_Backend_Block_Widget_Form_Renderer_Fieldset_Element
{
    protected $_template = 'Mage_DesignEditor::editor/form/renderer/font.phtml';

    /**
     * @return array
     */
    public function getClasses()
    {
        $element = $this->getElement();

        $classes = array();
        $classes[] = 'fieldset';
        if ($element->getClass()) {
            $classes[] = $element->getClass();
        }

        return $classes;
    }
}
