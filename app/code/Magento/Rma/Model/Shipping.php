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
 */
class Magento_Rma_Model_Shipping extends Magento_Core_Model_Abstract
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
     * Rma data
     *
     * @var Magento_Rma_Helper_Data
     */
    protected $_rmaData = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Directory_Model_RegionFactory
     */
    protected $_regionFactory;

    /**
     * @var Magento_Shipping_Model_Shipment_ReturnFactory
     */
    protected $_returnFactory;

    /**
     * @var Magento_Shipping_Model_Config
     */
    protected $_shippingConfig;

    /**
     * @var Magento_Rma_Model_RmaFactory
     */
    protected $_rmaFactory;

    /**
     * @param Magento_Rma_Helper_Data $rmaData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Directory_Model_RegionFactory $regionFactory
     * @param Magento_Shipping_Model_Shipment_ReturnFactory $returnFactory
     * @param Magento_Shipping_Model_Config $shippingConfig
     * @param Magento_Rma_Model_RmaFactory $rmaFactory
     * @param Magento_Rma_Model_Resource_Shipping $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Rma_Helper_Data $rmaData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Directory_Model_RegionFactory $regionFactory,
        Magento_Shipping_Model_Shipment_ReturnFactory $returnFactory,
        Magento_Shipping_Model_Config $shippingConfig,
        Magento_Rma_Model_RmaFactory $rmaFactory,
        Magento_Rma_Model_Resource_Shipping $resource,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_orderFactory = $orderFactory;
        $this->_storeManager = $storeManager;
        $this->_regionFactory = $regionFactory;
        $this->_returnFactory = $returnFactory;
        $this->_shippingConfig = $shippingConfig;
        $this->_rmaFactory = $rmaFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Rma_Model_Resource_Shipping');
    }

    /**
     * Processing object before save data
     *
     * @return Magento_Rma_Model_Shipping
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
     * @return Magento_Object
     * @throws Magento_Core_Exception
     */
    public function requestToShipment()
    {
        $shipmentStoreId = $this->getRma()->getStoreId();
        $storeInfo = new Magento_Object(
            $this->_coreStoreConfig->getConfig('general/store_information', $shipmentStoreId)
        );
        /** @var $order Magento_Sales_Model_Order */
        $order = $this->_orderFactory->create()->load($this->getRma()->getOrderId());
        $shipperAddress = $order->getShippingAddress();
        /** @var Magento_Sales_Model_Quote_Address $recipientAddress */
        $recipientAddress = $this->_rmaData->getReturnAddressModel($this->getRma()->getStoreId());
        list($carrierCode, $shippingMethod) = explode('_', $this->getCode(), 2);
        $shipmentCarrier = $this->_rmaData->getCarrier($this->getCode(), $shipmentStoreId);
        $baseCurrencyCode = $this->_storeManager->getStore($shipmentStoreId)->getBaseCurrencyCode();

        if (!$shipmentCarrier) {
            throw new Magento_Core_Exception(__('Invalid carrier: %1', $carrierCode));
        }
        $shipperRegionCode = $this->_regionFactory->create()->load($shipperAddress->getRegionId())->getCode();
        $recipientRegionCode = $recipientAddress->getRegionId();
        $recipientContactName = $this->_rmaData->getReturnContactName($this->getRma()->getStoreId());

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
            throw new Magento_Core_Exception(
                __('We need more information to create your shipping label(s). Please verify your store information and shipping settings.')
            );
        }

        /** @var $request Magento_Shipping_Model_Shipment_Return */
        $request = $this->_returnFactory->create();
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
        $carrierInstance = $this->_shippingConfig->getCarrierInstance($this->getCarrierCode());
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
            /** @var $rma Magento_Rma_Model_Rma */
            $rma = $this->_rmaFactory->create()->load($this->getRmaEntityId());
        }
        return (string)$rma->getProtectCode();
    }

    /**
     * Retrieves shipping label for current rma
     *
     * @var Magento_Rma_Model_Rma|int $rma
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
     * Create Zend_Pdf_Page instance with image from $imageString. Supports JPEG, PNG, GIF, WBMP, and GD2 formats.
     *
     * @param string $imageString
     * @return Zend_Pdf_Page|bool
     */
    public function createPdfPageFromImageString($imageString)
    {
        $image = imagecreatefromstring($imageString);
        if (!$image) {
            return false;
        }

        $xSize = imagesx($image);
        $ySize = imagesy($image);
        $page = new Zend_Pdf_Page($xSize, $ySize);

        imageinterlace($image, 0);
        $tmpFileName = sys_get_temp_dir() . DS . 'shipping_labels_'
                     . uniqid(mt_rand()) . time() . '.png';
        imagepng($image, $tmpFileName);
        $pdfImage = Zend_Pdf_Image::imageWithPath($tmpFileName);
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
