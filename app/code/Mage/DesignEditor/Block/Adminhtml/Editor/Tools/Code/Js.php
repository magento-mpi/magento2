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
 * Block that renders JS tab
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js extends Mage_Backend_Block_Widget_Form
{
    /**
     * @var Mage_Theme_Model_Config_Customization
     */
    protected $_customizationConfig;

    /**
     * @var Mage_DesignEditor_Model_Theme_Context
     */
    protected $_themeContext;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Theme_Model_Config_Customization $customizationConfig
     * @param Mage_DesignEditor_Model_Theme_Context $themeContext
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Theme_Model_Config_Customization $customizationConfig,
        Mage_DesignEditor_Model_Theme_Context $themeContext,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_customizationConfig = $customizationConfig;
        $this->_themeContext = $themeContext;
    }

    /**
     * Create a form element with necessary controls
     *
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'action' => '#',
            'method' => 'post'
        ));
        $this->setForm($form);
        $form->setUseContainer(true);

        $form->addType('js_files', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Uploader');

        $jsConfig = array(
            'name'     => 'js_files_uploader',
            'title'    => $this->__('Select JS Files to Upload'),
            'accept'   => 'application/x-javascript',
            'multiple' => '1',
        );
        if ($this->_customizationConfig->isThemeAssignedToStore($this->_themeContext->getEditableTheme())) {
            $confirmMessage = $this->__('These JavaScript files may change the appearance of your live store(s).'
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
        return $this->__('Are you sure you want to delete this JavaScript file?'
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
     * @return Mage_Core_Model_Resource_Theme_File_Collection
     */
    public function getFiles()
    {
        $customization = $this->_themeContext->getStagingTheme()->getCustomization();
        $jsFiles = $customization->getFilesByType(Mage_Core_Model_Theme_Customization_File_Js::TYPE);
        return $this->helper('Mage_Core_Helper_Data')->jsonEncode($customization->generateFileInfo($jsFiles));
    }

    /**
     * Get js tab title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->__('Custom javascript files');
    }
}
