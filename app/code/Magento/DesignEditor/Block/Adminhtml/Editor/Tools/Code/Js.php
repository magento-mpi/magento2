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
 * Block that renders JS tab
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Theme_Model_Config_Customization
     */
    protected $_customizationConfig;

    /**
     * @var Magento_DesignEditor_Model_Theme_Context
     */
    protected $_themeContext;

    /**
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Theme_Model_Config_Customization $customizationConfig
     * @param Magento_DesignEditor_Model_Theme_Context $themeContext
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Theme_Model_Config_Customization $customizationConfig,
        Magento_DesignEditor_Model_Theme_Context $themeContext,
        array $data = array()
    ) {
        parent::__construct($formFactory, $coreData, $context, $data);
        $this->_customizationConfig = $customizationConfig;
        $this->_themeContext = $themeContext;
    }

    /**
     * Create a form element with necessary controls
     *
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'action' => '#',
            'method' => 'post'
        ));
        $this->setForm($form);
        $form->setUseContainer(true);

        $form->addType('js_files', 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Uploader');

        $jsConfig = array(
            'name'     => 'js_files_uploader',
            'title'    => __('Select JS Files to Upload'),
            'accept'   => 'application/x-javascript',
            'multiple' => '1',
        );
        if ($this->_customizationConfig->isThemeAssignedToStore($this->_themeContext->getEditableTheme())) {
            $confirmMessage = __('These JavaScript files may change the appearance of your live store(s).'
                . ' Are you sure you want to do this?');
            $jsConfig['onclick'] = "return confirm('{$confirmMessage}');";
        }
        $form->addField('js_files_uploader', 'js_files', $jsConfig);

        parent::_prepareForm();
        return $this;
    }

    /**
     * Return confirmation message for delete action
     *
     * @return string
     */
    public function getConfirmMessageDelete()
    {
        return __('Are you sure you want to delete this JavaScript file?'
            . ' The changes to your theme will not be reversible.');
    }

    /**
     * Get upload js url
     *
     * @return string
     */
    public function getJsUploadUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/uploadjs',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Get reorder js url
     *
     * @return string
     */
    public function getJsReorderUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/reorderjs',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId()));
    }

    /**
     * Get delete js url
     *
     * @return string
     */
    public function getJsDeleteUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/deleteCustomFiles', array(
            'theme_id' => $this->_themeContext->getEditableTheme()->getId()
        ));
    }

    /**
     * Get custom js files
     *
     * @return Magento_Core_Model_Resource_Theme_File_Collection
     */
    public function getFiles()
    {
        $customization = $this->_themeContext->getStagingTheme()->getCustomization();
        $jsFiles = $customization->getFilesByType(Magento_Core_Model_Theme_Customization_File_Js::TYPE);
        return $this->helper('Magento_Core_Helper_Data')->jsonEncode($customization->generateFileInfo($jsFiles));
    }

    /**
     * Get js tab title
     *
     * @return string
     */
    public function getTitle()
    {
        return __('Custom javascript files');
    }
}
