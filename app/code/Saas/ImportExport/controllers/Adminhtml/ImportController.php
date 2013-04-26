<?php
/**
 * Import Controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once 'Mage/ImportExport/controllers/Adminhtml/ImportController.php';

class Saas_ImportExport_Adminhtml_ImportController extends Mage_ImportExport_Adminhtml_ImportController
{
    /**
     * @var Saas_ImportExport_Helper_Import_Validation
     */
    protected $_importValidationHelper;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Saas_ImportExport_Helper_Import_Validation $importHelper
     * @param string $areaCode
     * @param array $invokeArgs
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Saas_ImportExport_Helper_Import_Validation $importHelper,
        $areaCode = null,
        array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $objectManager, $frontController, $layoutFactory, $areaCode,
            $invokeArgs);

        $this->_importValidationHelper = $importHelper;
    }

    /**
     * Validate uploaded files actionBlock
     */
    public function validateAction()
    {
        if ($this->_importValidationHelper->isInProgress()) {
            $this->loadLayout(false);
            /** @var $resultBlock Mage_ImportExport_Block_Adminhtml_Import_Frame_Result */
            $resultBlock = $this->getLayout()->getBlock('import.frame.result');
            $resultBlock->addError($this->__('There is import data check in progress. Please, try again later.'));
            $this->renderLayout();
        } else {
            if ($this->getRequest()->isPost()) {
                $this->_importValidationHelper->setInProgress()
                    ->registerShutdownFunction();
                try {
                    parent::validateAction();
                } catch (Exception $e) {
                    $this->_importValidationHelper->setInProgress(false);
                    throw $e;
                }
                $this->_importValidationHelper->setInProgress(false);
            } else {
                parent::validateAction();
            }
        }
    }
}
