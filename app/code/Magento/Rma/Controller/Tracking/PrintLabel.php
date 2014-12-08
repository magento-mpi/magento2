<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Tracking;

use Magento\Framework\App\Action\NotFoundException;
use Magento\Framework\App\Filesystem\DirectoryList;

class PrintLabel extends \Magento\Rma\Controller\Tracking
{
    /**
     * @var \Magento\Rma\Model\Shipping\LabelService
     */
    protected $labelService;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileResponseFactory
     * @param \Magento\Rma\Model\Shipping\LabelService $labelService
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileResponseFactory,
        \Magento\Rma\Model\Shipping\LabelService $labelService
    ) {
        $this->labelService = $labelService;
        parent::__construct($context, $coreRegistry, $fileResponseFactory);
    }

    /**
     * Print label for one specific shipment
     *
     * @return \Magento\Framework\App\ResponseInterface|void
     * @throws \Magento\Framework\App\Action\NotFoundException
     */
    public function execute()
    {
        try {
            $data = $this->_objectManager->get(
                'Magento\Rma\Helper\Data'
            )->decodeTrackingHash(
                $this->getRequest()->getParam('hash')
            );

            $rmaIncrementId = '';
            if ($data['key'] == 'rma_id') {
                $this->_loadValidRma($data['id']);
                if ($this->_coreRegistry->registry('current_rma')) {
                    $rmaIncrementId = $this->_coreRegistry->registry('current_rma')->getIncrementId();
                }
            }
            $pdfContent = $this->labelService->getShippingLabelByRmaPdf($this->_coreRegistry->registry('current_rma'));
            return $this->_fileResponseFactory->create(
                'ShippingLabel(' . $rmaIncrementId . ').pdf',
                $pdfContent,
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->messageManager->addError(__('Something went wrong creating a shipping label.'));
        }
        throw new NotFoundException();
    }
}
