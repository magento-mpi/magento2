<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Tracking;

use Magento\Framework\App\Filesystem\DirectoryList;

class PackagePrint extends \Magento\Rma\Controller\Tracking
{
    /**
     * Create pdf document with information about packages
     *
     * @return void
     */
    public function execute()
    {
        /** @var $rmaHelper \Magento\Framework\Stdlib\DateTime\DateTime */
        $rmaHelper = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime');
        $data = $rmaHelper->decodeTrackingHash($this->getRequest()->getParam('hash'));
        if ($data['key'] == 'rma_id') {
            $this->_loadValidRma($data['id']);
        }

        /** @var $shippingInfoModel \Magento\Rma\Model\Shipping\Info */
        $shippingInfoModel = $this->_objectManager->create('Magento\Rma\Model\Shipping\Info');
        $shippingInfoModel->loadPackage($this->getRequest()->getParam('hash'));
        if ($shippingInfoModel) {
            /** @var $orderPdf \Magento\Shipping\Model\Order\Pdf\Packaging */
            $orderPdf = $this->_objectManager->create('Magento\Shipping\Model\Order\Pdf\Packaging');
            $block = $this->_view->getLayout()->getBlockSingleton(
                'Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod'
            );
            $orderPdf->setPackageShippingBlock($block);
            $pdf = $orderPdf->getPdf($shippingInfoModel);
            /** @var $dateModel \Magento\Framework\Stdlib\DateTime\DateTime */
            $dateModel = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime');
            $this->_fileResponseFactory->create(
                'packingslip' . $dateModel->date('Y-m-d_H-i-s') . '.pdf',
                $pdf->render(),
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        }
    }
}
