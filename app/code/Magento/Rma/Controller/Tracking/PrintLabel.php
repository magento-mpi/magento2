<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Tracking;

use \Magento\Framework\App\Action\NotFoundException;

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
            /** @var $shippingInfoModel \Magento\Rma\Model\Shipping\Info */
            $shippingInfoModel = $this->_objectManager->create('Magento\Rma\Model\Shipping\Info');
            $model = $shippingInfoModel->loadPackage($this->getRequest()->getParam('hash'));
            /** @var $shipping \Magento\Rma\Model\Shipping */
            $shipping = $this->_objectManager->create('Magento\Rma\Model\Shipping');
            $labelContent = $model->getShippingLabel();
            if ($labelContent) {
                $pdfContent = null;
                if (stripos($labelContent, '%PDF-') !== false) {
                    $pdfContent = $labelContent;
                } else {
                    $pdf = new \Zend_Pdf();
                    $page = $this->labelService->createPdfPageFromImageString($labelContent);
                    if (!$page) {
                        $this->messageManager->addError(
                            __(
                                "We don't recognize or support the file extension in shipment %1.",
                                $shipping->getIncrementId()
                            )
                        );
                    }
                    $pdf->pages[] = $page;
                    $pdfContent = $pdf->render();
                }

                return $this->_fileResponseFactory->create(
                    'ShippingLabel(' . $rmaIncrementId . ').pdf',
                    $pdfContent,
                    \Magento\Framework\App\Filesystem::VAR_DIR,
                    'application/pdf'
                );
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->messageManager->addError(__('Something went wrong creating a shipping label.'));
        }
        throw new NotFoundException();
    }
}
