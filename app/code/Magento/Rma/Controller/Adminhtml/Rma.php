<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Rma\Model\Rma as RmaModel;

class Rma extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Application filesystem
     *
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    /**
     * Read directory
     *
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    protected $readDirectory;

    /**
     * Http response file factory
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * Shipping carrier helper
     *
     * @var \Magento\Shipping\Helper\Carrier
     */
    protected $carrierHelper;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Shipping\Helper\Carrier $carrierHelper
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Shipping\Helper\Carrier $carrierHelper
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->filesystem = $filesystem;
        $this->readDirectory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::MEDIA_DIR);
        $this->_fileFactory = $fileFactory;
        $this->carrierHelper = $carrierHelper;
        parent::__construct($context);
    }

    /**
     * Init active menu and set breadcrumb
     *
     * @return \Magento\Rma\Controller\Adminhtml\Rma
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Rma::sales_magento_rma_rma');

        $this->_title->add(__('Returns'));
        return $this;
    }

    /**
     * Initialize model
     *
     * @param string $requestParam
     * @return \Magento\Rma\Model\Rma
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _initModel($requestParam = 'id')
    {
        /** @var $model \Magento\Rma\Model\Rma */
        $model = $this->_objectManager->create('Magento\Rma\Model\Rma');
        $model->setStoreId($this->getRequest()->getParam('store', 0));

        $rmaId = $this->getRequest()->getParam($requestParam);
        if ($rmaId) {
            $model->load($rmaId);
            if (!$model->getId()) {
                throw new \Magento\Framework\Model\Exception(__('The wrong RMA was requested.'));
            }
            $this->_coreRegistry->register('current_rma', $model);
            $orderId = $model->getOrderId();
        } else {
            $orderId = $this->getRequest()->getParam('order_id');
        }

        if ($orderId) {
            /** @var $order \Magento\Sales\Model\Order */
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            if (!$order->getId()) {
                throw new \Magento\Framework\Model\Exception(__('This is the wrong RMA order ID.'));
            }
            $this->_coreRegistry->register('current_order', $order);
        }

        return $model;
    }

    /**
     * Initialize model
     *
     * @return \Magento\Rma\Model\Rma\Create
     */
    protected function _initCreateModel()
    {
        /** @var $model \Magento\Rma\Model\Rma\Create */
        $model = $this->_objectManager->create('Magento\Rma\Model\Rma\Create');
        $orderId = $this->getRequest()->getParam('order_id');
        $model->setOrderId($orderId);
        if ($orderId) {
            /** @var $order \Magento\Sales\Model\Order */
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            $model->setCustomerId($order->getCustomerId());
            $model->setStoreId($order->getStoreId());
        }
        $this->_coreRegistry->register('rma_create_model', $model);
        return $model;
    }

    /**
     * Filter RMA save request
     *
     * @param array $saveRequest
     * @return array
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _filterRmaSaveRequest(array $saveRequest)
    {
        if (!isset($saveRequest['items'])) {
            throw new \Magento\Framework\Model\Exception(__('We failed to save this RMA. No items have been specified.'));
        }
        $saveRequest['items'] = $this->_filterRmaItems($saveRequest['items']);
        return $saveRequest;
    }

    /**
     * Filter user provided RMA items
     *
     * @param array $rawItems
     * @return array
     */
    protected function _filterRmaItems(array $rawItems)
    {
        $items = array();
        foreach ($rawItems as $key => $itemData) {
            if (!isset(
                $itemData['qty_authorized']
            ) && !isset(
                $itemData['qty_returned']
            ) && !isset(
                $itemData['qty_approved']
            ) && !isset(
                $itemData['qty_requested']
            )
            ) {
                continue;
            }
            $itemData['entity_id'] = strpos($key, '_') === false ? $key : false;
            $items[$key] = $itemData;
        }
        return $items;
    }

    /**
     * Check the permission
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Rma::magento_rma');
    }

    /**
     * Create shipping label for specific shipment with validation.
     *
     * @param \Magento\Rma\Model\Rma $model
     * @return bool
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _createShippingLabel(\Magento\Rma\Model\Rma $model)
    {
        $data = $this->getRequest()->getPost();
        if ($model && isset($data['packages']) && !empty($data['packages'])) {
            /** @var $shippingModel \Magento\Rma\Model\Shipping */
            $shippingModel = $this->_objectManager->create('Magento\Rma\Model\Shipping');
            /** @var $shipment \Magento\Rma\Model\Shipping */
            $shipment = $shippingModel->getShippingLabelByRma($model);

            $carrier = $this->_objectManager->get(
                'Magento\Rma\Helper\Data'
            )->getCarrier(
                $data['code'],
                $model->getStoreId()
            );
            if (!$carrier->isShippingLabelsAvailable()) {
                return false;
            }
            $shipment->setPackages($data['packages']);
            $shipment->setCode($data['code']);

            list($carrierCode, $methodCode) = explode('_', $data['code'], 2);
            $shipment->setCarrierCode($carrierCode);
            $shipment->setMethodCode($data['code']);

            $shipment->setCarrierTitle($data['carrier_title']);
            $shipment->setMethodTitle($data['method_title']);
            $shipment->setPrice($data['price']);
            $shipment->setRma($model);
            $shipment->setIncrementId($model->getIncrementId());
            $weight = 0;
            foreach ($data['packages'] as $package) {
                $weight += $package['params']['weight'];
            }
            $shipment->setWeight($weight);

            $response = $shipment->requestToShipment();

            if (!$response->hasErrors() && $response->hasInfo()) {
                $labelsContent = array();
                $trackingNumbers = array();
                $info = $response->getInfo();

                foreach ($info as $inf) {
                    if (!empty($inf['tracking_number']) && !empty($inf['label_content'])) {
                        $labelsContent[] = $inf['label_content'];
                        $trackingNumbers[] = $inf['tracking_number'];
                    }
                }
                $outputPdf = $this->_combineLabelsPdf($labelsContent);
                $shipment->setPackages(serialize($data['packages']));
                $shipment->setShippingLabel($outputPdf->render());
                $shipment->setIsAdmin(\Magento\Rma\Model\Shipping::IS_ADMIN_STATUS_ADMIN_LABEL);
                $shipment->setRmaEntityId($model->getId());
                $shipment->save();

                $carrierCode = $carrier->getCarrierCode();
                $carrierTitle = $this->carrierHelper->getCarrierConfigValue(
                    $carrierCode,
                    'title',
                    $shipment->getStoreId()
                );
                if ($trackingNumbers) {
                    /** @var $shippingResource \Magento\Rma\Model\Resource\Shipping */
                    $shippingResource = $this->_objectManager->create('Magento\Rma\Model\Resource\Shipping');
                    $shippingResource->deleteTrackingNumbers($model);
                    foreach ($trackingNumbers as $trackingNumber) {
                        /** @var $shippingModel \Magento\Rma\Model\Shipping */
                        $shippingModel = $this->_objectManager->create('Magento\Rma\Model\Shipping');
                        $shippingModel->setTrackNumber(
                            $trackingNumber
                        )->setCarrierCode(
                            $carrierCode
                        )->setCarrierTitle(
                            $carrierTitle
                        )->setRmaEntityId(
                            $model->getId()
                        )->setIsAdmin(
                            \Magento\Rma\Model\Shipping::IS_ADMIN_STATUS_ADMIN_LABEL_TRACKING_NUMBER
                        )->save();
                    }
                }
                return true;
            } else {
                throw new \Magento\Framework\Model\Exception($response->getErrors());
            }
        }
        return false;
    }

    /**
     * Create \Zend_Pdf_Page instance with image from $imageString. Supports JPEG, PNG, GIF, WBMP, and GD2 formats.
     *
     * @param string $imageString
     * @return \Zend_Pdf_Page|bool
     */
    protected function _createPdfPageFromImageString($imageString)
    {
        $image = imagecreatefromstring($imageString);
        if (!$image) {
            return false;
        }

        $xSize = imagesx($image);
        $ySize = imagesy($image);
        $page = new \Zend_Pdf_Page($xSize, $ySize);

        imageinterlace($image, 0);
        $dir = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::SYS_TMP_DIR);
        $tmpFileName = 'shipping_labels_' . uniqid(\Magento\Framework\Math\Random::getRandomNumber()) . time() . '.png';
        $tmpFilePath = $dir->getAbsolutePath($tmpFileName);
        imagepng($image, $tmpFilePath);
        $pdfImage = \Zend_Pdf_Image::imageWithPath($tmpFilePath);
        $page->drawImage($pdfImage, 0, 0, $xSize, $ySize);
        $dir->delete($tmpFileName);
        return $page;
    }

    /**
     * Combine Labels Pdf
     *
     * @param string[] $labelsContent
     * @return \Zend_Pdf
     */
    protected function _combineLabelsPdf(array $labelsContent)
    {
        $outputPdf = new \Zend_Pdf();
        foreach ($labelsContent as $content) {
            if (stripos($content, '%PDF-') !== false) {
                $pdfLabel = \Zend_Pdf::parse($content);
                foreach ($pdfLabel->pages as $page) {
                    $outputPdf->pages[] = clone $page;
                }
            } else {
                $page = $this->_createPdfPageFromImageString($content);
                if ($page) {
                    $outputPdf->pages[] = $page;
                }
            }
        }
        return $outputPdf;
    }
}
