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
 * Export controller
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ImportExport_Controller_Adminhtml_Export extends Magento_Adminhtml_Controller_Action
{
    /**
     * Initialize layout.
     *
     * @return Magento_ImportExport_Controller_Adminhtml_Export
     */
    protected function _initAction()
    {
        $this->_title(__('Import/Export'))
            ->loadLayout()
            ->_setActiveMenu('Magento_ImportExport::system_convert_export');

        return $this;
    }

    /**
     * Check access (in the ACL) for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_ImportExport::export');
    }

    /**
     * Load data with filter applying and create file for download.
     *
     * @return Magento_ImportExport_Controller_Adminhtml_Export
     */
    public function exportAction()
    {
        if ($this->getRequest()->getPost(Magento_ImportExport_Model_Export::FILTER_ELEMENT_GROUP)) {
            try {
                /** @var $model Magento_ImportExport_Model_Export */
                $model = $this->_objectManager->create('Magento_ImportExport_Model_Export');
                $model->setData($this->getRequest()->getParams());

                return $this->_prepareDownloadResponse(
                    $model->getFileName(),
                    $model->export(),
                    $model->getContentType()
                );
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
                $this->_getSession()->addError(__('Please correct the data sent.'));
            }
        } else {
            $this->_getSession()->addError(__('Please correct the data sent.'));
        }
        return $this->_redirect('*/*/index');
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_title(__('Export'))
            ->_addBreadcrumb(__('Export'), __('Export'));

        $this->renderLayout();
    }

    /**
     * Get grid-filter of entity attributes action.
     *
     * @return void
     */
    public function getFilterAction()
    {
        $data = $this->getRequest()->getParams();
        if ($this->getRequest()->isXmlHttpRequest() && $data) {
            try {
                $this->loadLayout();

                /** @var $attrFilterBlock Magento_ImportExport_Block_Adminhtml_Export_Filter */
                $attrFilterBlock = $this->getLayout()->getBlock('export.filter');
                /** @var $export Magento_ImportExport_Model_Export */
                $export = $this->_objectManager->create('Magento_ImportExport_Model_Export');
                $export->setData($data);

                $export->filterAttributeCollection(
                    $attrFilterBlock->prepareCollection(
                        $export->getEntityAttributeCollection()
                    )
                );
                $this->renderLayout();
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        } else {
            $this->_getSession()->addError(__('Please correct the data sent.'));
        }
        $this->_redirect('*/*/index');
    }
}
