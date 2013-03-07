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
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_BackgroundUploader
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer
{
    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'Mage_DesignEditor::editor/form/renderer/background-uploader.phtml';

    /**
     * Get URL of image upload action
     *
     * @return string
     */
    public function getImageUploadUrl()
    {
        //@TODO get rid of Registry usages

        return $this->getUrl('*/system_design_editor_tools/uploadQuickStyleImage',
            array('theme_id' => Mage::registry('theme')->getId())
        );
    }

    /**
     * Get URL of remove image action
     *
     * @return string
     */
    public function getImageRemoveUrl()
    {
        //@TODO get rid of Registry usages

        return $this->getUrl('*/system_design_editor_tools/removeQuickStyleImage',
            array('theme_id' => Mage::registry('theme')->getId())
        );
    }
}
