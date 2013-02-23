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
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Composite
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Recursive
{
    //former fieldset.phtml but split into several templates
    protected $_templates = array(
        'Mage_DesignEditor::editor/form/renderer/composite/children.phtml',
        'Mage_DesignEditor::editor/form/renderer/composite.phtml',
        'Mage_DesignEditor::editor/form/renderer/composite/wrapper.phtml',
    );

    /**
     * @return array
     */
    public function getCssClasses()
    {
        /** @var $element Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract */
        $element = $this->getElement();
        $isField = $element->getFieldsetType() == 'field';

        $cssClasses = array();
        $cssClasses[] = ($isField) ? 'field' : 'fieldset';
        if ($element->getClass()) {
            $cssClasses[] = $element->getClass();
        }
        if ($isField && $element->hasAdvanced()) {
            $cssClasses[] = 'complex';
        }

        return $cssClasses;
    }
}
