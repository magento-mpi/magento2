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
 * File uploader form element renderer
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Uploader
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_Recursive
{
    /**
     * Set of templates to render
     *
     * Upper is rendered first and is inserted into next using <?php echo $this->getHtml() ?>
     *
     * @var array
     */
    protected $_templates = array(
        'Mage_DesignEditor::editor/form/renderer/element/input.phtml',
    );
}
