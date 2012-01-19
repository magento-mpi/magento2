<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * DHL International (API v1.4) Label Creation
 *
 * @category Mage
 * @package  Mage_Usa
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf
{
    /**
     * @var SimpleXMLElement
     */
    protected $_info;

    /**
     * Dhl International Label Creation Class constructor
     *
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        $this->_info = $arguments['info'];
    }

    /**
     * Create Label
     *
     * @return string
     * @throws Zend_Pdf_Exception
     * @throws InvalidArgumentException
     */
    public function render()
    {
        $pdf = new Zend_Pdf();

        $pdfBuilder = new Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_PageBuilder();

        $template = new Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page(Zend_Pdf_Page::SIZE_A4_LANDSCAPE);
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
            ->addShipmentInformation()
            ->addDateInfo($this->_info->ShipmentDate)
            ->addWeightInfo((string)$this->_info->ChargeableWeight, (string)$this->_info->WeightUnit)
            ->addWaybillBarcode((string)$this->_info->AirwayBillNumber, (string)$this->_info->Barcodes->AWBBarCode)
            ->addRoutingBarcode(
                (string)$this->_info->DHLRoutingCode,
                (string)$this->_info->DHLRoutingDataId,
                (string)$this->_info->Barcodes->DHLRoutingBarCode
            )
            ->addBorder();

        foreach ($this->_info->Pieces->Piece as $piece) {
            $page = new Mage_Usa_Model_Shipping_Carrier_Dhl_Label_Pdf_Page($template);
            $pdfBuilder->setPage($page)
                ->addPieceNumber((int)$piece->PieceNumber, (int)$this->_info->Piece)
                ->addPieceIdBarcode(
                    (string)$piece->DataIdentifier,
                    (string)$piece->LicensePlate,
                    (string)$piece->LicensePlateBarCode
                );
            array_push($pdf->pages, $page);
        }
        return $pdf->render();
    }
}
