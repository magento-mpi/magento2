<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Creditmemo\AbstractCreditmemo;

use \Magento\Framework\App\ResponseInterface;

class Pdfcreditmemos extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_creditmemo');
    }

    /**
     * @return ResponseInterface|void
     */
    public function execute()
    {
        $creditmemosIds = $this->getRequest()->getPost('creditmemo_ids');
        if (!empty($creditmemosIds)) {
            $invoices = $this->_objectManager->create(
                'Magento\Sales\Model\Resource\Order\Creditmemo\Collection'
            )->addAttributeToSelect(
                '*'
            )->addAttributeToFilter(
                'entity_id',
                array('in' => $creditmemosIds)
            )->load();
            if (!isset($pdf)) {
                $pdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Creditmemo')->getPdf($invoices);
            } else {
                $pages = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Creditmemo')->getPdf($invoices);
                $pdf->pages = array_merge($pdf->pages, $pages->pages);
            }
            $date = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->date('Y-m-d_H-i-s');

            return $this->_fileFactory->create(
                'creditmemo' . $date . '.pdf',
                $pdf->render(),
                \Magento\Framework\App\Filesystem::VAR_DIR,
                'application/pdf'
            );
        }
        return $this->_redirect('sales/*/');
    }
}
