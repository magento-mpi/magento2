<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * DHL International (API v1.4) Label Creation
 *
 * @category Magento
 * @package  Magento_Usa
 * @author   Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Usa\Model\Shipping\Carrier\Dhl\Label;

class Pdf
{
    /**
     * Label Information
     *
     * @var \SimpleXMLElement
     */
    protected $_info;

    /**
     * Shipment Request
     *
     * @var \Magento\Shipping\Model\Shipment\Request
     */
    protected $_request;

    /**
     * Dhl International Label Creation Class constructor
     *
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        $this->_info    = $arguments['info'];
        $this->_request = $arguments['request'];
    }

    /**
     * Create Label
     *
     * @return string
     */
    public function render()
    {
        $pdf = new \Zend_Pdf();

        $pdfBuilder = new \Magento\Usa\Model\Shipping\Carrier\Dhl\Label\Pdf\PageBuilder();

        $template = new \Magento\Usa\Model\Shipping\Carrier\Dhl\Label\Pdf\Page(\Zend_Pdf_Page::SIZE_A4_LANDSCAPE);
        $pdfBuilder->setPage($template)
            ->addProductName((string)$this->_info->ProductShortName)
            ->addProductContentCode((string)$this->_info->ProductContentCode)
        //->addUnitId({unitId})
        //->addReferenceData({referenceData})
            ->addSenderInfo($this->_info->Shipper)
            ->addOriginInfo((string)$this->_info->OriginServiceArea->ServiceAreaCode)
            ->addReceiveInfo($this->_info->Consignee)
            ->addDestinationFacilityCode(
                (string)$this->_info->Consignee->CountryCode,
                (string)$this->_info->DestinationServiceArea->ServiceAreaCode,
                (string)$this->_info->DestinationServiceArea->FacilityCode
            )
            ->addServiceFeaturesCodes()
            ->addDeliveryDateCode()
            ->addShipmentInformation($this->_request->getOrderShipment())
            ->addDateInfo($this->_info->ShipmentDate)
            ->addWeightInfo((string)$this->_info->ChargeableWeight, (string)$this->_info->WeightUnit)
            ->addWaybillBarcode((string)$this->_info->AirwayBillNumber, (string)$this->_info->Barcodes->AWBBarCode)
            ->addRoutingBarcode(
                (string)$this->_info->DHLRoutingCode,
                (string)$this->_info->DHLRoutingDataId,
                (string)$this->_info->Barcodes->DHLRoutingBarCode
            )
            ->addBorder();

        $packages = array_values($this->_request->getPackages());
        $i = 0;
        foreach ($this->_info->Pieces->Piece as $piece) {
            $page = new \Magento\Usa\Model\Shipping\Carrier\Dhl\Label\Pdf\Page($template);
            $pdfBuilder->setPage($page)
                ->addPieceNumber((int)$piece->PieceNumber, (int)$this->_info->Piece)
                ->addContentInfo($packages[$i])
                ->addPieceIdBarcode(
                    (string)$piece->DataIdentifier,
                    (string)$piece->LicensePlate,
                    (string)$piece->LicensePlateBarCode
                );
            array_push($pdf->pages, $page);
            $i++;
        }
        return $pdf->render();
    }
}
