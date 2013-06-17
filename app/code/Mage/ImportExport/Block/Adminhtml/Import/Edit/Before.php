<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block before edit form
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Block_Adminhtml_Import_Edit_Before extends Mage_Backend_Block_Template
{
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
     * @param Mage_Core_Helper_Data $coreHelper
     * @param Mage_ImportExport_Model_Import $importModel
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Core_Helper_Data $coreHelper,
        Mage_ImportExport_Model_Import $importModel,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_coreHelper = $coreHelper;
        $this->_importModel = $importModel;
    }

    /**
     * Returns json-encoded entity behaviors array
     *
     * @return string
     */
    public function getEntityBehaviors()
    {
        $importModel = $this->_importModel;
        $behaviors = $importModel::getEntityBehaviors();
        foreach ($behaviors as $entityCode => $behavior) {
            $behaviors[$entityCode] = $behavior['code'];
        }
        return $this->_coreHelper->jsonEncode($behaviors);
    }

    /**
     * Return json-encoded list of existing behaviors
     *
     * @return string
     */
    public function getUniqueBehaviors()
    {
        $importModel = $this->_importModel;
        $uniqueBehaviors = $importModel::getUniqueEntityBehaviors();
        return $this->_coreHelper->jsonEncode(array_keys($uniqueBehaviors));
    }
}
