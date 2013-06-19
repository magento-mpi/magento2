<?php
/**
 * Import Edit Image block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Block_Adminhtml_Import_Edit_Image extends Mage_Backend_Block_Template
{
    /**
     * @var Saas_ImportExport_Helper_Import_Image_Configuration
     */
    protected $_configuration;

    /**
     * Core helper
     *
     * @var Mage_Core_Helper_Data
     */
    protected $_coreHelper;

    /**
     * Basic import model
     *
     * @var Mage_ImportExport_Model_Import
     */
    protected $_importModel;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Saas_ImportExport_Helper_Import_Image_Configuration $configuration
     * @param Mage_Core_Helper_Data $coreHelper
     * @param Mage_ImportExport_Model_Import $importModel
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Saas_ImportExport_Helper_Import_Image_Configuration $configuration,
        Mage_Core_Helper_Data $coreHelper,
        Mage_ImportExport_Model_Import $importModel,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_configuration = $configuration;
        $this->_coreHelper = $coreHelper;
        $this->_importModel = $importModel;
    }

    /**
     * Get type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        return $this->_configuration->getTypeCode();
    }

    /**
     * Return json-encoded list of existing behaviors
     *
     * @return string
     */
    public function getUniqueBehaviorsAsJson()
    {
        $importModel = $this->_importModel;
        $uniqueBehaviors = $importModel::getUniqueEntityBehaviors();

        return $this->_coreHelper->jsonEncode(array_keys($uniqueBehaviors));
    }

    /**
     * Get edit form action
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('*/import_images/import');
    }
}
