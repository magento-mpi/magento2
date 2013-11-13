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
namespace Magento\ImportExport\Controller\Adminhtml;

class Export extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * Initialize layout.
     *
     * @return \Magento\ImportExport\Controller\Adminhtml\Export
     */
    protected function _initAction()
    {
        $this->_title->add(__('Import/Export'))
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
     * @return \Magento\ImportExport\Controller\Adminhtml\Export
     */
    public function exportAction()
    {
        if ($this->getRequest()->getPost(\Magento\ImportExport\Model\Export::FILTER_ELEMENT_GROUP)) {
            try {
                /** @var $model \Magento\ImportExport\Model\Export */
                $model = $this->_objectManager->create('Magento\ImportExport\Model\Export');
                $model->setData($this->getRequest()->getParams());

                return $this->_fileFactory->create(
                    $model->getFileName(),
                    $model->export(),
                    $model->getContentType()
                );
            } catch (\Magento\Core\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->_objectManager->get('Magento\Logger')->logException($e);
                $this->_getSession()->addError(__('Please correct the data sent.'));
            }
        } else {
            $this->_getSession()->addError(__('Please correct the data sent.'));
        }
        return $this->_redirect('adminhtml/*/index');
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_title->add(__('Export'))
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

                /** @var $attrFilterBlock \Magento\ImportExport\Block\Adminhtml\Export\Filter */
                $attrFilterBlock = $this->_layoutServices->getLayout()->getBlock('export.filter');
                /** @var $export \Magento\ImportExport\Model\Export */
                $export = $this->_objectManager->create('Magento\ImportExport\Model\Export');
                $export->setData($data);

                $export->filterAttributeCollection(
                    $attrFilterBlock->prepareCollection(
                        $export->getEntityAttributeCollection()
                    )
                );
                $this->renderLayout();
                return;
            } catch (\Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        } else {
            $this->_getSession()->addError(__('Please correct the data sent.'));
        }
        $this->_redirect('adminhtml/*/index');
    }
}
