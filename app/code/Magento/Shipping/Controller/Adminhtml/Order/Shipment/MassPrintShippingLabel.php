<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Controller\Adminhtml\Order\Shipment;

use \Magento\Framework\App\ResponseInterface;
use \Magento\Backend\App\Action;

class MassPrintShippingLabel extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Shipping\Model\Shipping\LabelGenerator
     */
    protected $labelGenerator;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->labelGenerator = $labelGenerator;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::shipment');
    }

    /**
     * Batch print shipping labels for whole shipments.
     * Push pdf document with shipping labels to user browser
     *
     * @return ResponseInterface|void
     */
    public function execute()
    {
        $request = $this->getRequest();
        $ids = $request->getParam('order_ids');
        $createdFromOrders = !empty($ids);
        $shipments = null;
        $labelsContent = array();
        switch ($request->getParam('massaction_prepare_key')) {
            case 'shipment_ids':
                $ids = $request->getParam('shipment_ids');
                array_filter($ids, 'intval');
                if (!empty($ids)) {
                    $shipments = $this->_objectManager->create(
                        'Magento\Sales\Model\Resource\Order\Shipment\Collection'
                    )->addFieldToFilter(
                        'entity_id',
                        array('in' => $ids)
                    );
                }
                break;
            case 'order_ids':
                $ids = $request->getParam('order_ids');
                array_filter($ids, 'intval');
                if (!empty($ids)) {
                    $shipments = $this->_objectManager->create(
                        'Magento\Sales\Model\Resource\Order\Shipment\Collection'
                    )->setOrderFilter(
                        array('in' => $ids)
                    );
                }
                break;
        }

        if ($shipments && $shipments->getSize()) {
            foreach ($shipments as $shipment) {
                $labelContent = $shipment->getShippingLabel();
                if ($labelContent) {
                    $labelsContent[] = $labelContent;
                }
            }
        }

        if (!empty($labelsContent)) {
            $outputPdf = $this->labelGenerator->combineLabelsPdf($labelsContent);
            return $this->_fileFactory->create(
                'ShippingLabels.pdf',
                $outputPdf->render(),
                \Magento\Framework\App\Filesystem::VAR_DIR,
                'application/pdf'
            );
        }

        if ($createdFromOrders) {
            $this->messageManager->addError(__('There are no shipping labels related to selected orders.'));
            $this->_redirect('sales/order/index');
        } else {
            $this->messageManager->addError(__('There are no shipping labels related to selected shipments.'));
            $this->_redirect('sales/shipment/index');
        }
    }
}
