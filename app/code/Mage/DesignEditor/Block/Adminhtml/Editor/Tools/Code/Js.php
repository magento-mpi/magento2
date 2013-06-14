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
 *
 * @method Mage_Core_Model_Theme getTheme()
 * @method setTheme($theme)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools_Code_Js extends Mage_Backend_Block_Widget_Form
{
    /**
     * @var Mage_Core_Model_Theme_Service
     */
    protected $_service;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Core_Model_Theme_Service $service
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Core_Model_Theme_Service $service,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_service = $service;
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
            'multiple' => '',
        );
        if ($this->_service->isThemeAssignedToStore($this->getTheme())) {
            $confirmMessage = $this->__('You are about to upload JavaScript files. '
                . 'This will take effect immediately and might affect the design of your store if your theme '
                . 'is assigned to the store front. Are you sure you want to do this?');
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
            . ' The results to your theme will not be reversible.');
    }

    /**
     * Get upload js url
     *
     * @return string
     */
    public function getJsUploadUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/uploadjs', array('theme_id' => $this->getTheme()->getId()));
    }

    /**
     * Get reorder js url
     *
     * @return string
     */
    public function getJsReorderUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/reorderjs', array('theme_id' => $this->getTheme()->getId()));
    }

    /**
     * Get delete js url
     *
     * @return string
     */
    public function getJsDeleteUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/deleteCustomFiles', array(
            'theme_id' => $this->getTheme()->getId()
        ));
    }

    /**
     * Get custom js files
     *
     * @return Mage_Core_Model_Resource_Theme_File_Collection
     */
    public function getJsFiles()
    {
        return $this->getTheme()->getCustomizationData(Mage_Core_Model_Theme_Customization_Files_Js::TYPE);
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
