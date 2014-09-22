<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model;

/**
 * RMA Shipping Model
 */
class Shipping extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Store address
     */
    const XML_PATH_ADDRESS1 = 'sales/magento_rma/address';

    const XML_PATH_ADDRESS2 = 'sales/magento_rma/address1';

    const XML_PATH_CITY = 'sales/magento_rma/city';

    const XML_PATH_REGION_ID = 'sales/magento_rma/region_id';

    const XML_PATH_ZIP = 'sales/magento_rma/zip';

    const XML_PATH_COUNTRY_ID = 'sales/magento_rma/country_id';

    const XML_PATH_CONTACT_NAME = 'sales/magento_rma/store_name';

    /**
     * Constants - value of is_admin field in table
     */
    const IS_ADMIN_STATUS_USER_TRACKING_NUMBER = 0;

    const IS_ADMIN_STATUS_ADMIN_TRACKING_NUMBER = 1;

    const IS_ADMIN_STATUS_ADMIN_LABEL = 2;

    const IS_ADMIN_STATUS_ADMIN_LABEL_TRACKING_NUMBER = 3;

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
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData = null;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Sales order factory
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * Core store manager interface
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Directory region factory
     *
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * Shipping return shipment factory
     *
     * @var \Magento\Shipping\Model\Shipment\ReturnShipmentFactory
     */
    protected $_returnFactory;

    /**
     * Shipping carrier factory
     *
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * Rma factory
     *
     * @var \Magento\Rma\Model\RmaFactory
     */
    protected $_rmaFactory;

    /**
     * Application filesystem
     *
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Shipping\Model\Shipment\ReturnShipmentFactory $returnFactory
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param \Magento\Rma\Model\RmaFactory $rmaFactory
     * @param Resource\Shipping $resource
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Shipping\Model\Shipment\ReturnShipmentFactory $returnFactory,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        \Magento\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Rma\Model\Resource\Shipping $resource,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        $this->_scopeConfig = $scopeConfig;
        $this->_orderFactory = $orderFactory;
        $this->_storeManager = $storeManager;
        $this->_regionFactory = $regionFactory;
        $this->_returnFactory = $returnFactory;
        $this->_carrierFactory = $carrierFactory;
        $this->filesystem = $filesystem;
        $this->_rmaFactory = $rmaFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Rma\Model\Resource\Shipping');
    }

    /**
     * Processing object before save data
     *
     * @return $this
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
     * @return \Magento\Framework\Object
     * @throws \Magento\Framework\Model\Exception
     */
    public function requestToShipment()
    {
        $shipmentStoreId = $this->getRma()->getStoreId();
        $storeInfo = new \Magento\Framework\Object(
            $this->_scopeConfig->getValue('general/store_information', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $shipmentStoreId)
        );
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_orderFactory->create()->load($this->getRma()->getOrderId());
        $shipperAddress = $order->getShippingAddress();
        /** @var \Magento\Sales\Model\Quote\Address $recipientAddress */
        $recipientAddress = $this->_rmaData->getReturnAddressModel($this->getRma()->getStoreId());
        list($carrierCode, $shippingMethod) = explode('_', $this->getCode(), 2);
        $shipmentCarrier = $this->_rmaData->getCarrier($this->getCode(), $shipmentStoreId);
        $baseCurrencyCode = $this->_storeManager->getStore($shipmentStoreId)->getBaseCurrencyCode();

        if (!$shipmentCarrier) {
            throw new \Magento\Framework\Model\Exception(__('Invalid carrier: %1', $carrierCode));
        }
        $shipperRegionCode = $this->_regionFactory->create()->load($shipperAddress->getRegionId())->getCode();
        $recipientRegionCode = $recipientAddress->getRegionId();
        $recipientContactName = $this->_rmaData->getReturnContactName($this->getRma()->getStoreId());

        if (!$recipientContactName->getName() ||
            !$recipientContactName->getLastName() ||
            !$recipientAddress->getCompany() ||
            !$storeInfo->getPhone() ||
            !$recipientAddress->getStreetFull() ||
            !$recipientAddress->getCity() ||
            !$shipperRegionCode ||
            !$recipientAddress->getPostcode() ||
            !$recipientAddress->getCountryId()
        ) {
            throw new \Magento\Framework\Model\Exception(
                __(
                    'We need more information to create your shipping label(s). Please verify your store information and shipping settings.'
                )
            );
        }

        /** @var $request \Magento\Shipping\Model\Shipment\ReturnShipment */
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

        $referenceData = 'RMA #' . $request->getOrderShipment()->getRma()->getIncrementId() . ' P';
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
        $carrierInstance = $this->_carrierFactory->create($this->getCarrierCode());
        if (!$carrierInstance) {
            $custom = array();
            $custom['title'] = $this->getCarierTitle();
            $custom['number'] = $this->getTrackNumber();
            return $custom;
        } else {
            $carrierInstance->setStore($this->getStore());
        }

        if (!($trackingInfo = $carrierInstance->getTrackingInfo($this->getTrackNumber()))) {
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
            /** @var $rma Rma */
            $rma = $this->_rmaFactory->create()->load($this->getRmaEntityId());
        }
        return (string)$rma->getProtectCode();
    }

    /**
     * Retrieves shipping label for current rma
     *
     * @param Rma|int $rma
     * @return \Magento\Framework\Object
     */
    public function getShippingLabelByRma($rma)
    {
        if (!is_int($rma)) {
            $rma = $rma->getId();
        }
        $label = $this->getCollection()->addFieldToFilter(
            'rma_entity_id',
            $rma
        )->addFieldToFilter(
            'is_admin',
            self::IS_ADMIN_STATUS_ADMIN_LABEL
        )->getFirstItem();

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
     * Check whether custom carrier was used for this track
     *
     * @return bool
     */
    public function isCustom()
    {
        return $this->getCarrierCode() == self::CUSTOM_CARRIER_CODE;
    }
}
