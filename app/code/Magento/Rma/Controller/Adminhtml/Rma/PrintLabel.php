<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

use Magento\Framework\App\Filesystem\DirectoryList;

class PrintLabel extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Print label for one specific shipment
     *
     * @return void|\Magento\Backend\App\Action
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        try {
            $model = $this->_initModel();
            /** @var $shippingModel \Magento\Rma\Model\Shipping */
            $shippingModel = $this->_objectManager->create('Magento\Rma\Model\Shipping');
            $labelContent = $shippingModel->getShippingLabelByRma($model)->getShippingLabel();
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
                                $model->getIncrementId()
                            )
                        );
                    }
                    $pdf->pages[] = $page;
                    $pdfContent = $pdf->render();
                }

                return $this->_fileFactory->create(
                    'ShippingLabel(' . $model->getIncrementId() . ').pdf',
                    $pdfContent,
                    DirectoryList::MEDIA_DIR,
                    'application/pdf'
                );
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->messageManager->addError(__('Something went wrong creating a shipping label.'));
        }
        $this->_redirect('adminhtml/*/edit', array('id' => $this->getRequest()->getParam('id')));
    }
}
