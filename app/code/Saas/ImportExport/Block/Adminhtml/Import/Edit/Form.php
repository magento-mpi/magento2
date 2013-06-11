<?php
/**
 * Import edit form block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Block_Adminhtml_Import_Edit_Form extends Mage_ImportExport_Block_Adminhtml_Import_Edit_Form
{
    /**
     * @var Saas_ImportExport_Helper_Import_Image_Configuration
     */
    protected $_configuration;

    /**
     * @var Saas_ImportExport_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Saas_ImportExport_Helper_Import_Image_Configuration $configuration
     * @param Saas_ImportExport_Helper_Data $helper
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Saas_ImportExport_Helper_Import_Image_Configuration $configuration,
        Saas_ImportExport_Helper_Data $helper,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_configuration = $configuration;
        $this->_helper = $helper;
    }

    /**
     * Add fieldsets
     *
     * @return Mage_ImportExport_Block_Adminhtml_Import_Edit_Form
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('upload_file_fieldset');

        $note = $this->__('Allowed file type: ZIP. Max file size: %dM', $this->_helper->getMaxFileSizeInMb());
        $fieldset->addField($this->_configuration->getFileFieldName(), 'file', array(
            'name' => $this->_configuration->getFileFieldName(),
            'label' => $this->__('Select Image Archive File to Import'),
            'title' => $this->__('Select Image Archive File to Import'),
            'note' => $note,
            'required' => false,
        ));

        return $this;
    }
}
