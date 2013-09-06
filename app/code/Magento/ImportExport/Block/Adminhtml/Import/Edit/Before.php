<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block before edit form
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Block_Adminhtml_Import_Edit_Before extends Magento_Backend_Block_Template
{
    /**
     * Core helper
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreHelper;

    /**
     * Basic import model
     *
     * @var Magento_ImportExport_Model_Import
     */
    protected $_importModel;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Helper_Data $coreHelper
     * @param Magento_ImportExport_Model_Import $importModel
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Helper_Data $coreHelper,
        Magento_ImportExport_Model_Import $importModel,
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
        $behaviors = $this->_importModel->getEntityBehaviors();
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
        $uniqueBehaviors = $this->_importModel->getUniqueEntityBehaviors();
        return $this->_coreHelper->jsonEncode(array_keys($uniqueBehaviors));
    }
}
