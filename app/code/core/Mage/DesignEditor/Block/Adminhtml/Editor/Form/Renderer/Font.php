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
 * Composite 'font' element renderer
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Font
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer
{
    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'Mage_DesignEditor::editor/form/renderer/font.phtml';

    /**
     * Get element CSS classes
     *
     * @return array
     */
    public function getClasses()
    {
        /** @var $element Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Font */
        $element = $this->getElement();

        $classes = array();
        $classes[] = 'fieldset';
        if ($element->getClass()) {
            $classes[] = $element->getClass();
        }

        return $classes;
    }
}
