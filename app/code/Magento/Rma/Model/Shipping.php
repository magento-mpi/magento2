<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA Shipping Model
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Model;

class Shipping extends \Magento\Core\Model\AbstractModel
{
    /**
     * Store address
     */
    const XML_PATH_ADDRESS1             = 'sales/magento_rma/address';
    const XML_PATH_ADDRESS2             = 'sales/magento_rma/address1';
    const XML_PATH_CITY                 = 'sales/magento_rma/city';
    const XML_PATH_REGION_ID            = 'sales/magento_rma/region_id';
    const XML_PATH_ZIP                  = 'sales/magento_rma/zip';
    const XML_PATH_COUNTRY_ID           = 'sales/magento_rma/country_id';
    const XML_PATH_CONTACT_NAME         = 'sales/magento_rma/store_name';

    /**
     * Constants - value of is_admin field in table
     */
    const IS_ADMIN_STATUS_USER_TRACKING_NUMBER          = 0;
    const IS_ADMIN_STATUS_ADMIN_TRACKING_NUMBER         = 1;
    const IS_ADMIN_STATUS_ADMIN_LABEL                   = 2;
    const IS_ADMIN_STATUS_ADMIN_LABEL_TRACKING_NUMBER   = 3;

    /**
     * Code of custom carrier
     */
    const CUSTOM_CARRIER_CODE = 'custom';

    /**
     * Tracking info
     *
     * @var array
     */
    protected $_trackingInfo = array();

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\Rma\Model\Resource\Shipping');
    }

    /**
     * Processing object before save data
     *
     * @return \Magento\Rma\Model\Shipping
     */
    protected function _beforeSave()
    {
        if (is_null($this->getIsAdmin())) {
            $this->setIsAdmin(self::IS_ADMIN_STATUS_USER_TRACKING_NUMBER);
        }
        return $this;
    }
    /**
     * Prepare and do return of shipment
     *
     * @return \Magento\Object
     */
    public function requestToShipment()
    {
        $shipmentStoreId    = $this->getRma()->getStoreId();
        $storeInfo          = new \Magento\Object(\Mage::getStoreConfig('general/store_information', $shipmentStoreId));

        /** @var $order \Magento\Sales\Model\Order */
        $order              = \Mage::getModel('Magento\Sales\Model\Order')->load($this->getRma()->getOrderId());
        $shipperAddress     = $order->getShippingAddress();
        /** @var \Magento\Sales\Model\Quote\Address $recipientAddress */
        $recipientAddress   = \Mage::helper('Magento\Rma\Helper\Data')->getReturnAddressModel($this->getRma()->getStoreId());

        list($carrierCode, $shippingMethod) = explode('_', $this->getCode(), 2);

        $shipmentCarrier    = \Mage::helper('Magento\Rma\Helper\Data')->getCarrier($this->getCode(), $shipmentStoreId);
        $baseCurrencyCode   = \Mage::app()->getStore($shipmentStoreId)->getBaseCurrencyCode();

        if (!$shipmentCarrier) {
            \Mage::throwException(__('Invalid carrier: %1', $carrierCode));
        }

        $shipperRegionCode  = \Mage::getModel('Magento\Directory\Model\Region')->load($shipperAddress->getRegionId())->getCode();

        $recipientRegionCode= $recipientAddress->getRegionId();

        $recipientContactName = \Mage::helper('Magento\Rma\Helper\Data')->getReturnContactName($this->getRma()->getStoreId());

        if (!$recipientContactName->getName()
            || !$recipientContactName->getLastName()
            || !$recipientAddress->getCompany()
            || !$storeInfo->getPhone()
            || !$recipientAddress->getStreetFull()
            || !$recipientAddress->getCity()
            || !$shipperRegionCode
            || !$recipientAddress->getPostcode()
            || !$recipientAddress->getCountryId()
        ) {
            \Mage::throwException(
                __('We need more information to create your shipping label(s). Please verify your store information and shipping settings.')
            );
        }

        /** @var $request \Magento\Shipping\Model\Shipment\Request */
        $request = \Mage::getModel('Magento\Shipping\Model\Shipment\Return');
        $request->setOrderShipment($this);

        $request->setShipperContactPersonName($order->getCustomerName());
        $request->setShipperContactPersonFirstName($order->getCustomerFirstname());
        $request->setShipperContactPersonLastName($order->getCustomerLastname());

        $companyName = $shipperAddress->getCompany();
        if (empty($companyName)) {
            $companyName = $order->getCustomerName();
        }
        $request->setShipperContactCompanyName($companyName);
        $request->setShipperContactPhoneNumber($shipperAddress->getTelephone());
        $request->setShipperEmail($shipperAddress->getEmail());
        $request->setShipperAddressStreet($shipperAddress->getStreetFull());
        $request->setShipperAddressStreet1($shipperAddress->getStreet1());
        $request->setShipperAddressStreet2($shipperAddress->getStreet2());
        $request->setShipperAddressCity($shipperAddress->getCity());
        $request->setShipperAddressStateOrProvinceCode($shipperRegionCode);
        $request->setShipperAddressPostalCode($shipperAddress->getPostcode());
        $request->setShipperAddressCountryCode($shipperAddress->getCountryId());

        $request->setRecipientContactPersonName($recipientContactName->getName());
        $request->setRecipientContactPersonFirstName($recipientContactName->getFirstName());
        $request->setRecipientContactPersonLastName($recipientContactName->getLastName());
        $request->setRecipientContactCompanyName($recipientAddress->getCompany());
        $request->setRecipientContactPhoneNumber($storeInfo->getPhone());
        $request->setRecipientEmail($recipientAddress->getEmail());
        $request->setRecipientAddressStreet($recipientAddress->getStreetFull());
        $request->setRecipientAddressStreet1($recipientAddress->getStreet(1));
        $request->setRecipientAddressStreet2($recipientAddress->getStreet(2));
        $request->setRecipientAddressCity($recipientAddress->getCity());
        $request->setRecipientAddressStateOrProvinceCode($recipientRegionCode);
        $request->setRecipientAddressRegionCode($recipientRegionCode);
        $request->setRecipientAddressPostalCode($recipientAddress->getPostcode());
        $request->setRecipientAddressCountryCode($recipientAddress->getCountryId());

        $request->setShippingMethod($shippingMethod);
        $request->setPackageWeight($this->getWeight());
        $request->setPackages($this->getPackages());
        $request->setBaseCurrencyCode($baseCurrencyCode);
        $request->setStoreId($shipmentStoreId);

        $referenceData = 'RMA #'. $request->getOrderShipment()->getRma()->getIncrementId(). ' P';
        $request->setReferenceData($referenceData);

        return $shipmentCarrier->returnOfShipment($request);
    }

    /**
     * Retrieve detail for shipment track
     *
     * @return string
     */
    public function getNumberDetail()
    {
        $carrierInstance = \Mage::getSingleton('Magento\Shipping\Model\Config')->getCarrierInstance($this->getCarrierCode());
        if (!$carrierInstance) {
            $custom = array();
            $custom['title']  = $this->getCarierTitle();
            $custom['number'] = $this->getTrackNumber();
            return $custom;
        } else {
            $carrierInstance->setStore($this->getStore());
        }

        if (!$trackingInfo = $carrierInstance->getTrackingInfo($this->getTrackNumber())) {
            return __('No detail for number "%1"', $this->getTrackNumber());
        }

        return $trackingInfo;
    }

    /**
     * Retrieve hash code of current order
     *
     * @return string
     */
    public function getProtectCode()
    {
        if ($this->getRmaEntityId()) {
            $rma = \Mage::getModel('Magento\Rma\Model\Rma')->load($this->getRmaEntityId());
        }

        return (string)$rma->getProtectCode();
    }

    /**
     * Retrieves shipping label for current rma
     *
     * @var \Magento\Rma\Model\Rma|int $rma
     * @return string
     */
    public function getShippingLabelByRma($rma)
    {
        if (!is_int($rma)) {
            $rma = $rma->getId();
        }
        $label = $this->getCollection()
            ->addFieldToFilter('rma_entity_id', $rma)
            ->addFieldToFilter('is_admin', self::IS_ADMIN_STATUS_ADMIN_LABEL)
            ->getFirstItem();

        if ($label->getShippingLabel()) {
            $label->setShippingLabel(
                $this->getResource()->getReadConnection()->decodeVarbinary($label->getShippingLabel())
            );
        }

        return $label;
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
        $tmpFileName = sys_get_temp_dir() . DS . 'shipping_labels_'
                     . uniqid(mt_rand()) . time() . '.png';
        imagepng($image, $tmpFileName);
        $pdfImage = \Zend_Pdf_Image::imageWithPath($tmpFileName);
        $page->drawImage($pdfImage, 0, 0, $xSize, $ySize);
        unlink($tmpFileName);
        return $page;
    }

    /**
     * Check whether custom carrier was used for this track
     *
     * @return bool
     */
    public function isCustom()
    {
        return $this->getCarrierCode() == self::CUSTOM_CARRIER_CODE;
    }
}
