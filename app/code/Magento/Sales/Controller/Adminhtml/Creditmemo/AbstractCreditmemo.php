<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Controller\Adminhtml\Creditmemo;

class AbstractCreditmemo extends \Magento\Backend\App\Action
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
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Sales\Controller\Adminhtml\Creditmemo
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Sales::sales_creditmemo')
            ->_addBreadcrumb(__('Sales'), __('Sales'))
            ->_addBreadcrumb(__('Credit Memos'), __('Credit Memos'));
        return $this;
    }

    /**
     * Creditmemos grid
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->_view->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Creditmemo'));
        $this->_view->renderLayout();
    }

    /**
     * Creditmemo information page
     */
    public function viewAction()
    {
        if ($creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            $this->_forward('view', 'order_creditmemo', null, array('come_from' => 'sales_creditmemo'));
        } else {
            $this->_forward('noroute');
        }
    }

    /**
     * Notify user
     */
    public function emailAction()
    {
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        if ($creditmemoId) {
            $creditmemo = $this->_objectManager->create('Magento\Sales\Model\Order\Creditmemo')->load($creditmemoId);
            if ($creditmemo) {
                $creditmemo->sendEmail();
                $historyItem = $this->_objectManager->create(
                    'Magento\Sales\Model\Resource\Order\Status\History\Collection'
                )->getUnnotifiedForInstance($creditmemo, \Magento\Sales\Model\Order\Creditmemo::HISTORY_ENTITY_NAME);
                if ($historyItem) {
                    $historyItem->setIsCustomerNotified(1);
                    $historyItem->save();
                }

                $this->messageManager->addSuccess(__('We sent the message.'));
                $this->_redirect('sales/order_creditmemo/view', array(
                    'creditmemo_id' => $creditmemoId
                ));
            }
        }
    }

    public function pdfcreditmemosAction()
    {
        $creditmemosIds = $this->getRequest()->getPost('creditmemo_ids');
        if (!empty($creditmemosIds)) {
            $invoices = $this->_objectManager->create('Magento\Sales\Model\Resource\Order\Creditmemo\Collection')
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $creditmemosIds))
                ->load();
            if (!isset($pdf)) {
                $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Creditmemo')->getPdf($invoices);
            } else {
                $pages = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Creditmemo')->getPdf($invoices);
                $pdf->pages = array_merge($pdf->pages, $pages->pages);
            }
            $date = $this->_objectManager->get('Magento\Stdlib\DateTime\DateTime')->date('Y-m-d_H-i-s');

            return $this->_fileFactory->create(
                'creditmemo' . $date . '.pdf',
                $pdf->render(),
                \Magento\App\Filesystem::VAR_DIR,
                'application/pdf'
            );
        }
        $this->_redirect('sales/*/');
    }

    public function printAction()
    {
        /** @see \Magento\Sales\Controller\Adminhtml\Order\Invoice */
        $creditmemoId = $this->getRequest()->getParam('creditmemo_id');
        if ($creditmemoId) {
            $creditmemo = $this->_objectManager->create('Magento\Sales\Model\Order\Creditmemo')->load($creditmemoId);
            if ($creditmemo) {
                $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Creditmemo')
                    ->getPdf(array($creditmemo));
                $date = $this->_objectManager->get('Magento\Stdlib\DateTime\DateTime')->date('Y-m-d_H-i-s');
                return $this->_fileFactory->create(
                    'creditmemo' . $date . '.pdf',
                    $pdf->render(),
                    \Magento\App\Filesystem::VAR_DIR,
                    'application/pdf'
                );
            }
        } else {
            $this->_forward('noroute');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_creditmemo');
    }
}
