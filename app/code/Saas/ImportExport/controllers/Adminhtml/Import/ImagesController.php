<?php
/**
 * Import Images Controller
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Adminhtml_Import_ImagesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Saas_ImportExport_Model_Service_Image_Import
     */
    protected $_importService;

    /**
     * @var Saas_ImportExport_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Backend_Controller_Context $context
     * @param Saas_ImportExport_Model_Service_Image_Import $importService
     * @param Saas_ImportExport_Helper_Data $helper
     * @param string $areaCode
     */
    public function __construct(
        Mage_Backend_Controller_Context $context,
        Saas_ImportExport_Model_Service_Image_Import $importService,
        Saas_ImportExport_Helper_Data $helper,
        $areaCode = null
    ) {
        parent::__construct($context, $areaCode);

        $this->_importService = $importService;
        $this->_helper = $helper;
    }

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_ImportExport::export');
    }

    /**
     * Upload Image Archive Action
     *
     * @return void
     */
    public function importAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->loadLayout(false);
            /** @var $resultBlock Mage_ImportExport_Block_Adminhtml_Import_Frame_Result */
            $resultBlock = $this->getLayout()->getBlock('import.frame.result');

            if (empty($_FILES)) {
                $resultBlock->addError($this->__('File was not uploaded.'));
            } else {
                try {
                    $result = $this->_importService->import();

                    $errors = $result->getErrorsAsString();
                    if ($errors) {
                        $resultBlock->addNotice($errors);
                    }

                    $summary = $result->getUploadSummary();
                    if ($summary) {
                        if ($summary['is_success']) {
                            $resultBlock->addSuccess($summary['message']);

                            $this->_helper->cleanPageCache();
                        } else {
                            $resultBlock->addError($summary['message']);
                        }
                    }
                } catch (Saas_ImportExport_Model_Import_Image_Exception $e) {
                    $resultBlock->addError($e->getMessage());
                }
            }
            $this->renderLayout();
        } else {
            $this->_getSession()->addError($this->__('Data is invalid or file is not uploaded'));
            $this->_redirect('adminhtml/import/index');
        }
    }
}
