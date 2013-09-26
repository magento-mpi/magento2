<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Controller;

class Tracking extends \Magento\Core\Controller\Front\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Popup action
     * Shows tracking info if it's present, otherwise redirects to 404
     *
     * @return null
     */
    public function popupAction()
    {
        /** @var $shippingInfoModel \Magento\Rma\Model\Shipping\Info */
        $shippingInfoModel = $this->_objectManager->create('Magento\Rma\Model\Shipping\Info');
        $shippingInfoModel->loadByHash($this->getRequest()->getParam('hash'));

        $this->_coreRegistry->register('rma_current_shipping', $shippingInfoModel);
        if (count($shippingInfoModel->getTrackingInfo()) == 0) {
            $this->norouteAction();
            return;
        }
        $this->loadLayout();
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Tracking Information'));
        }
        $this->renderLayout();
    }

    /**
     * Popup package action
     * Shows package info if it's present, otherwise redirects to 404
     *
     * @return null
     */
    public function packageAction()
    {
        /** @var $shippingInfoModel \Magento\Rma\Model\Shipping\Info */
        $shippingInfoModel = $this->_objectManager->create('Magento\Rma\Model\Shipping\Info');
        $shippingInfoModel->loadPackage($this->getRequest()->getParam('hash'));

        $this->_coreRegistry->register('rma_package_shipping', $shippingInfoModel);
        if (!$shippingInfoModel->getPackages()) {
            $this->norouteAction();
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Check order view availability
     *
     * @param \Magento\Rma\Model\Rma $rma
     * @return bool
     */
    protected function _canViewRma($rma)
    {
        if (!$this->_customerSession->isLoggedIn()) {
            $currentOrder = $this->_coreRegistry->registry('current_order');
            if ($rma->getOrderId() && ($rma->getOrderId() === $currentOrder->getId())) {
                return true;
            }
            return false;
        } else {
            return true;
        }
    }

    /**
     * Try to load valid rma by entity_id and register it
     *
     * @param int $entityId
     * @return bool
     */
    protected function _loadValidRma($entityId = null)
    {
        if (!$this->_customerSession->isLoggedIn()
            && !$this->_objectManager->get('Magento\Sales\Helper\Guest')->loadValidOrder()
        ) {
            return;
        }

        if (null === $entityId) {
            $entityId = (int) $this->getRequest()->getParam('entity_id');
        }

        if (!$entityId) {
            $this->_forward('noRoute');
            return false;
        }

        /** @var $rma \Magento\Rma\Model\Rma */
        $rma = $this->_objectManager->create('Magento\Rma\Model\Rma')->load($entityId);
        if ($this->_canViewRma($rma)) {
            $this->_coreRegistry->register('current_rma', $rma);
            return true;
        } else {
            $this->_redirect('*/*/returns');
        }
        return false;
    }

    /**
     * Print label for one specific shipment
     */
    public function printLabelAction()
    {
        try {
            $data = $this->_objectManager->get('Magento\Rma\Helper\Data')
                ->decodeTrackingHash($this->getRequest()->getParam('hash'));

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
                    $page = $shipping->createPdfPageFromImageString($labelContent);
                    if (!$page) {
                        $this->_getSession()->addError(__("We don't recognize or support the file extension in shipment %1.", $shipping->getIncrementId()));
                    }
                    $pdf->pages[] = $page;
                    $pdfContent = $pdf->render();
                }

                return $this->_prepareDownloadResponse(
                    'ShippingLabel(' . $rmaIncrementId . ').pdf',
                    $pdfContent,
                    'application/pdf'
                );
            }
        } catch (\Magento\Core\Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Core\Model\Logger')->logException($e);
            $this->_getSession()
                ->addError(__('Something went wrong creating a shipping label.'));
        }
        $this->norouteAction();
        return;
    }

    /**
     * Create pdf document with information about packages
     *
     */
    public function packagePrintAction()
    {
        /** @var $rmaHelper \Magento\Core\Model\Date */
        $rmaHelper = $this->_objectManager->get('Magento\Core\Model\Date');
        $data = $rmaHelper->decodeTrackingHash($this->getRequest()->getParam('hash'));
        if ($data['key'] == 'rma_id') {
            $this->_loadValidRma($data['id']);
        }

        /** @var $shippingInfoModel \Magento\Rma\Model\Shipping\Info */
        $shippingInfoModel = $this->_objectManager->create('Magento\Rma\Model\Shipping\Info');
        $shippingInfoModel->loadPackage($this->getRequest()->getParam('hash'));
        if ($shippingInfoModel) {
            /** @var $orderPdf \Magento\Sales\Model\Order\Pdf\Shipment\Packaging */
            $orderPdf = $this->_objectManager->create('Magento\Sales\Model\Order\Pdf\Shipment\Packaging');
            $block = $this->_layout->getBlockSingleton(
                'Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shippingmethod'
            );
            $orderPdf->setPackageShippingBlock($block);
            $pdf = $orderPdf->getPdf($shippingInfoModel);
            /** @var $dateModel \Magento\Core\Model\Date */
            $dateModel = $this->_objectManager->get('Magento\Core\Model\Date');
            $this->_prepareDownloadResponse(
                'packingslip' . $dateModel->date('Y-m-d_H-i-s') . '.pdf', $pdf->render(), 'application/pdf'
            );
        }
    }
}
