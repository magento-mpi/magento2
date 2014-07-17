<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class PrintPackage extends \Magento\Rma\Controller\Adminhtml\Rma
{
    /**
     * Create pdf document with information about packages
     *
     * @return void|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $model = $this->_initModel();
        /** @var $shippingModel \Magento\Rma\Model\Shipping */
        $shippingModel = $this->_objectManager->create('Magento\Rma\Model\Shipping');
        $shipment = $shippingModel->getShippingLabelByRma($model);

        if ($shipment) {
            /** @var $orderPdf \Magento\Shipping\Model\Order\Pdf\Packaging */
            $orderPdf = $this->_objectManager->create('Magento\Shipping\Model\Order\Pdf\Packaging');
            /** @var $block \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod */
            $block = $this->_view->getLayout()->getBlockSingleton(
                'Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod'
            );
            $orderPdf->setPackageShippingBlock($block);
            $pdf = $orderPdf->getPdf($shipment);
            /** @var $dateModel \Magento\Framework\Stdlib\DateTime\DateTime */
            $dateModel = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime');
            return $this->_fileFactory->create(
                'packingslip' . $dateModel->date('Y-m-d_H-i-s') . '.pdf',
                $pdf->render(),
                \Magento\Framework\App\Filesystem::MEDIA_DIR,
                'application/pdf'
            );
        } else {
            $this->_forward('noroute');
        }
    }
}
