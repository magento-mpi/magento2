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
 * Checkbox form element renderer
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Checkbox
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Recursive
{
    protected $_templates = array(
        'Mage_DesignEditor::editor/form/renderer/element/input.phtml',
        'Mage_DesignEditor::editor/form/renderer/checkbox-utility.phtml',
        'Mage_DesignEditor::editor/form/renderer/element/wrapper.phtml',
        'Mage_DesignEditor::editor/form/renderer/template.phtml',
    );
}
