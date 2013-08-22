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
 * Checkbox form element renderer
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Checkbox
    extends Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Recursive
{
    /**
     * Set of templates to render
     *
     * Upper is rendered first and is inserted into next using <?php echo $this->getHtml() ?>
     *
     * @var array
     */
    protected $_templates = array(
        'Magento_DesignEditor::editor/form/renderer/element/input.phtml',
        'Magento_DesignEditor::editor/form/renderer/checkbox-utility.phtml',
        'Magento_DesignEditor::editor/form/renderer/element/wrapper.phtml',
        'Magento_DesignEditor::editor/form/renderer/template.phtml',
    );
}
