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
 * Import controller
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Controller_Adminhtml_Import extends Magento_Adminhtml_Controller_Action
{
    /**
     * Initialize layout.
     *
     * @return Magento_ImportExport_Controller_Adminhtml_Import
     */
    protected function _initAction()
    {
        $this->_title(__('Import/Export'))
            ->loadLayout()
            ->_setActiveMenu('Magento_ImportExport::system_convert_import');
        return $this;
    }

    /**
     * Check access (in the ACL) for current user.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_ImportExport::import');
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_getSession()->addNotice($this->_objectManager->get('Magento_ImportExport_Helper_Data')
            ->getMaxUploadSizeMessage());
        $this->_initAction()->_title(__('Import'))->_addBreadcrumb(__('Import'), __('Import'));
        $this->renderLayout();
    }

    /**
     * Start import process action
     */
    public function startAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $this->loadLayout(false);

            /** @var $resultBlock Magento_ImportExport_Block_Adminhtml_Import_Frame_Result */
            $resultBlock = $this->getLayout()->getBlock('import.frame.result');
            /** @var $importModel Magento_ImportExport_Model_Import */
            $importModel = $this->_objectManager->create('Magento_ImportExport_Model_Import');

            try {
                $importModel->importSource();
                $importModel->invalidateIndex();
                $resultBlock->addAction('show', 'import_validation_container')
                    ->addAction('innerHTML', 'import_validation_container_header', __('Status'));
            } catch (Exception $e) {
                $resultBlock->addError($e->getMessage());
                $this->renderLayout();
                return;
            }
            $resultBlock->addAction('hide', array('edit_form', 'upload_button', 'messages'))
                ->addSuccess(__('Import successfully done'));
            $this->renderLayout();
        } else {
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Validate uploaded files action
     */
    public function validateAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $this->loadLayout(false);
            /** @var $resultBlock Magento_ImportExport_Block_Adminhtml_Import_Frame_Result */
            $resultBlock = $this->getLayout()->getBlock('import.frame.result');
            // common actions
            $resultBlock->addAction('show', 'import_validation_container')
                ->addAction('clear', array(
                    Magento_ImportExport_Model_Import::FIELD_NAME_SOURCE_FILE,
                    Magento_ImportExport_Model_Import::FIELD_NAME_IMG_ARCHIVE_FILE
            ));

            try {
                /** @var $import Magento_ImportExport_Model_Import */
                $import = $this->_objectManager->create('Magento_ImportExport_Model_Import')->setData($data);
                $source = Magento_ImportExport_Model_Import_Adapter::findAdapterFor($import->uploadSource());
                $validationResult = $import->validateSource($source);

                if (!$import->getProcessedRowsCount()) {
                    $resultBlock->addError(__('File does not contain data. Please upload another one'));
                } else {
                    if (!$validationResult) {
                        $this->_processValidationError($import, $resultBlock);
                    } else {
                        if ($import->isImportAllowed()) {
                            $resultBlock->addSuccess(
                                __('File is valid! To start import process press "Import" button'), true
                            );
                        } else {
                            $resultBlock->addError(
                                __('File is valid, but import is not possible'), false
                            );
                        }
                    }
                    $resultBlock->addNotice($import->getNotices());
                    $resultBlock->addNotice(
                        __('Checked rows: %1, checked entities: %2, invalid rows: %3, total errors: %4',
                            $import->getProcessedRowsCount(), $import->getProcessedEntitiesCount(),
                            $import->getInvalidRowsCount(), $import->getErrorsCount()
                        )
                    );
                }
            } catch (Exception $e) {
                $resultBlock->addNotice(__('Please fix errors and re-upload file.'))
                    ->addError($e->getMessage());
            }
            $this->renderLayout();
        } elseif ($this->getRequest()->isPost() && empty($_FILES)) {
            $this->loadLayout(false);
            $resultBlock = $this->getLayout()->getBlock('import.frame.result');
            $resultBlock->addError(__('File was not uploaded'));
            $this->renderLayout();
        } else {
            $this->_getSession()->addError(__('Data is invalid or file is not uploaded'));
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Process validation results
     *
     * @param Magento_ImportExport_Model_Import $import
     * @param Magento_ImportExport_Block_Adminhtml_Import_Frame_Result $resultBlock
     */
    protected function _processValidationError(Magento_ImportExport_Model_Import $import,
        Magento_ImportExport_Block_Adminhtml_Import_Frame_Result $resultBlock
    ) {
        if ($import->getProcessedRowsCount() == $import->getInvalidRowsCount()) {
            $resultBlock->addNotice(
                __('File is totally invalid. Please fix errors and re-upload file.')
            );
        } elseif ($import->getErrorsCount() >= $import->getErrorsLimit()) {
            $resultBlock->addNotice(
                __('Errors limit (%1) reached. Please fix errors and re-upload file.',
                    $import->getErrorsLimit()
                )
            );
        } else {
            if ($import->isImportAllowed()) {
                $resultBlock->addNotice(
                    __('Please fix errors and re-upload file or simply press "Import" button'
                        . ' to skip rows with errors'),
                    true
                );
            } else {
                $resultBlock->addNotice(
                    __('File is partially valid, but import is not possible'), false
                );
            }
        }
        // errors info
        foreach ($import->getErrors() as $errorCode => $rows) {
            $error = $errorCode . ' ' . __('in rows:') . ' ' . implode(', ', $rows);
            $resultBlock->addError($error);
        }
    }
}
