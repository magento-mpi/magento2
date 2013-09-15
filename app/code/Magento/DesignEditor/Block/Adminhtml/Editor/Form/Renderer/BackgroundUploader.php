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
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_BackgroundUploader
    extends Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer
{
    /**
     * @var Magento_DesignEditor_Model_Theme_Context
     */
    protected $_themeContext;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_DesignEditor_Model_Theme_Context $themeContext
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_DesignEditor_Model_Theme_Context $themeContext,
        array $data = array()
    ) {
        $this->_themeContext = $themeContext;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'Magento_DesignEditor::editor/form/renderer/background-uploader.phtml';

    /**
     * Get URL of image upload action
     *
     * @return string
     */
    public function getImageUploadUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/uploadQuickStyleImage',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId())
        );
    }

    /**
     * Get URL of remove image action
     *
     * @return string
     */
    public function getImageRemoveUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/removeQuickStyleImage',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId())
        );
    }
}
