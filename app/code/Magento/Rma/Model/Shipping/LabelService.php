<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Model\Shipping;

class LabelService
{
    /**
     * @var \Magento\Rma\Helper\Data
     */
    private $rmaHelper;

    /**
     * @var \Magento\Rma\Model\ShippingFactory
     */
    private $shippingFactory;

    /**
     * @var \Magento\Rma\Model\Resource\ShippingFactory
     */
    private $shippingResourceFactory;

    /**
     * Application filesystem
     *
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Magento\Rma\Helper\Data $rmaHelper
     * @param \Magento\Rma\Model\ShippingFactory $shippingFactory
     * @param \Magento\Rma\Model\Resource\ShippingFactory $shippingResourceFactory
     * @param \Magento\Framework\App\Filesystem $filesystem
     */
    public function __constructor(
        \Magento\Rma\Helper\Data $rmaHelper,
        \Magento\Rma\Model\ShippingFactory $shippingFactory,
        \Magento\Rma\Model\Resource\ShippingFactory $shippingResourceFactory,
        \Magento\Framework\App\Filesystem $filesystem
    ) {
        $this->rmaHelper = $rmaHelper;
        $this->shippingFactory = $shippingFactory;
        $this->shippingResourceFactory = $shippingResourceFactory;
        $this->filesystem = $filesystem;
    }

    /**
     * Create shipping label for specific shipment with validation
     *
     * @param \Magento\Rma\Model\Rma $rmaModel
     * @param array $data
     * @return bool
     * @throws \Magento\Framework\Model\Exception
     */
    public function createShippingLabel(\Magento\Rma\Model\Rma $rmaModel, $data = [])
    {
        if (empty($data['packages'])) {
            return false;
        }
        $carrier = $this->rmaHelper->getCarrier($data['code'], $rmaModel->getStoreId());
        if (!$carrier->isShippingLabelsAvailable()) {
            return false;
        }

        /** @var $shippingModel \Magento\Rma\Model\Shipping */
        $shippingModel = $this->shippingFactory->create();
        /** @var $shipment \Magento\Rma\Model\Shipping */
        $shipment = $shippingModel->getShippingLabelByRma($rmaModel);

        $shipment->setPackages($data['packages']);
        $shipment->setCode($data['code']);

        list($carrierCode, $methodCode) = explode('_', $data['code'], 2);
        $shipment->setCarrierCode($carrierCode);
        $shipment->setMethodCode($data['code']);

        $shipment->setCarrierTitle($data['carrier_title']);
        $shipment->setMethodTitle($data['method_title']);
        $shipment->setPrice($data['price']);
        $shipment->setRma($rmaModel);
        $shipment->setIncrementId($rmaModel->getIncrementId());
        $weight = 0;
        foreach ($data['packages'] as $package) {
            $weight += $package['params']['weight'];
        }
        $shipment->setWeight($weight);

        $response = $shipment->requestToShipment();

        if ($response->hasErrors() || $response->hasInfo()) {
            throw new \Magento\Framework\Model\Exception($response->getErrors());
        }

        $labelsContent = array();
        $trackingNumbers = array();
        $info = $response->getInfo();

        foreach ($info as $inf) {
            if (!empty($inf['tracking_number']) && !empty($inf['label_content'])) {
                $labelsContent[] = $inf['label_content'];
                $trackingNumbers[] = $inf['tracking_number'];
            }
        }
        $outputPdf = $this->combineLabelsPdf($labelsContent);
        $shipment->setPackages(serialize($data['packages']));
        $shipment->setShippingLabel($outputPdf->render());
        $shipment->setIsAdmin(\Magento\Rma\Model\Shipping::IS_ADMIN_STATUS_ADMIN_LABEL);
        $shipment->setRmaEntityId($rmaModel->getId());
        $shipment->save();

        if ($trackingNumbers) {
            /** @var $shippingResource \Magento\Rma\Model\Resource\Shipping */
            $shippingResource = $this->shippingResourceFactory->create();
            $shippingResource->deleteTrackingNumbers($rmaModel);
            foreach ($trackingNumbers as $trackingNumber) {
                /** @var $shippingModel \Magento\Rma\Model\Shipping */
                $shippingModel = $this->shippingFactory->create();
                $shippingModel->setTrackNumber(
                    $trackingNumber
                )->setCarrierCode(
                    $carrier->getCarrierCode()
                )->setCarrierTitle(
                    $carrier->getConfigData('title')
                )->setRmaEntityId(
                    $rmaModel->getId()
                )->setIsAdmin(
                    \Magento\Rma\Model\Shipping::IS_ADMIN_STATUS_ADMIN_LABEL_TRACKING_NUMBER
                )->save();
            }
        }
        return true;
    }

    /**
     * Combine Labels Pdf
     *
     * @param string[] $labelsContent
     * @return \Zend_Pdf
     */
    public function combineLabelsPdf(array $labelsContent)
    {
        $outputPdf = new \Zend_Pdf();
        foreach ($labelsContent as $content) {
            if (stripos($content, '%PDF-') !== false) {
                $pdfLabel = \Zend_Pdf::parse($content);
                foreach ($pdfLabel->pages as $page) {
                    $outputPdf->pages[] = clone $page;
                }
            } else {
                $page = $this->createPdfPageFromImageString($content);
                if ($page) {
                    $outputPdf->pages[] = $page;
                }
            }
        }
        return $outputPdf;
    }

    /**
     * Create \Zend_Pdf_Page instance with image from $imageString. Supports JPEG, PNG, GIF, WBMP, and GD2 formats.
     *
     * @param string $imageString
     * @return \Zend_Pdf_Page|bool
     */
    public function createPdfPageFromImageString($imageString)
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
}
