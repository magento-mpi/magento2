<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Shipping_Model_Shipping
{
    /**
     * Store address
     */
    const XML_PATH_STORE_ADDRESS1     = 'shipping/origin/street_line1';
    const XML_PATH_STORE_ADDRESS2     = 'shipping/origin/street_line2';
    const XML_PATH_STORE_CITY         = 'shipping/origin/city';
    const XML_PATH_STORE_REGION_ID    = 'shipping/origin/region_id';
    const XML_PATH_STORE_ZIP          = 'shipping/origin/postcode';
    const XML_PATH_STORE_COUNTRY_ID   = 'shipping/origin/country_id';

    /**
     * Default shipping orig for requests
     *
     * @var array
     */
    protected $_orig = null;

    /**
     * Cached result
     *
     * @var Magento_Sales_Model_Shipping_Method_Result
     */
    protected $_result = null;

    /**
     * Part of carrier xml config path
     *
     * @var string
     */
    protected $_availabilityConfigField = 'active';

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Shipping_Model_Config
     */
    protected $_shippingConfig;

    /**
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * @var Magento_Shipping_Model_Carrier_Factory
     */
    protected $_carrierFactory;

    /**
     * @var Magento_Shipping_Model_Rate_ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var Magento_Shipping_Model_Rate_RequestFactory
     */
    protected $_rateRequestFactory;

    /**
     * @var Magento_Directory_Model_RegionFactory
     */
    protected $_regionFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Shipping_Model_Config $shippingConfig
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_Shipping_Model_Carrier_Factory $carrierFactory
     * @param Magento_Shipping_Model_Rate_ResultFactory $rateResultFactory
     * @param Magento_Shipping_Model_Rate_RequestFactory $rateRequestFactory
     * @param Magento_Directory_Model_RegionFactory $regionFactory
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Shipping_Model_Config $shippingConfig,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_Shipping_Model_Carrier_Factory $carrierFactory,
        Magento_Shipping_Model_Rate_ResultFactory $rateResultFactory,
        Magento_Shipping_Model_Rate_RequestFactory $rateRequestFactory,
        Magento_Directory_Model_RegionFactory $regionFactory
    ) {
        $this->_coreData = $coreData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_shippingConfig = $shippingConfig;
        $this->_storeManager = $storeManager;
        $this->_authSession = $authSession;
        $this->_carrierFactory = $carrierFactory;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateRequestFactory = $rateRequestFactory;
        $this->_regionFactory = $regionFactory;
    }

    /**
     * Get shipping rate result model
     *
     * @return Magento_Shipping_Model_Rate_Result
     */
    public function getResult()
    {
        if (empty($this->_result)) {
            $this->_result = $this->_rateResultFactory->create();
        }
        return $this->_result;
    }

    /**
     * Set shipping orig data
     *
     * @param array $data
     * @return null
     */
    public function setOrigData($data)
    {
        $this->_orig = $data;
    }

    /**
     * Reset cached result
     *
     * @return Magento_Shipping_Model_Shipping
     */
    public function resetResult()
    {
        $this->getResult()->reset();
        return $this;
    }

    /**
     * Retrieve configuration model
     *
     * @return Magento_Shipping_Model_Config
     */
    public function getConfig()
    {
        return $this->_shippingConfig;
    }

    /**
     * Retrieve all methods for supplied shipping data
     *
     * @todo make it ordered
     * @param Magento_Shipping_Model_Rate_Request $request
     * @return Magento_Shipping_Model_Shipping
     */
    public function collectRates(Magento_Shipping_Model_Rate_Request $request)
    {
        $storeId = $request->getStoreId();
        if (!$request->getOrig()) {
            $request
                ->setCountryId($this->_coreStoreConfig->getConfig(self::XML_PATH_STORE_COUNTRY_ID, $request->getStore()))
                ->setRegionId($this->_coreStoreConfig->getConfig(self::XML_PATH_STORE_REGION_ID, $request->getStore()))
                ->setCity($this->_coreStoreConfig->getConfig(self::XML_PATH_STORE_CITY, $request->getStore()))
                ->setPostcode($this->_coreStoreConfig->getConfig(self::XML_PATH_STORE_ZIP, $request->getStore()));
        }

        $limitCarrier = $request->getLimitCarrier();
        if (!$limitCarrier) {
            $carriers = $this->_coreStoreConfig->getConfig('carriers', $storeId);

            foreach ($carriers as $carrierCode => $carrierConfig) {
                $this->collectCarrierRates($carrierCode, $request);
            }
        } else {
            if (!is_array($limitCarrier)) {
                $limitCarrier = array($limitCarrier);
            }
            foreach ($limitCarrier as $carrierCode) {
                $carrierConfig = $this->_coreStoreConfig->getConfig('carriers/' . $carrierCode, $storeId);
                if (!$carrierConfig) {
                    continue;
                }
                $this->collectCarrierRates($carrierCode, $request);
            }
        }

        return $this;
    }

    /**
     * Collect rates of given carrier
     *
     * @param string                           $carrierCode
     * @param Magento_Shipping_Model_Rate_Request $request
     * @return Magento_Shipping_Model_Shipping
     */
    public function collectCarrierRates($carrierCode, $request)
    {
        /* @var $carrier Magento_Shipping_Model_Carrier_Abstract */
        $carrier = $this->getCarrierByCode($carrierCode, $request->getStoreId());
        if (!$carrier) {
            return $this;
        }
        $carrier->setActiveFlag($this->_availabilityConfigField);
        $result = $carrier->checkAvailableShipCountries($request);
        if (false !== $result && !($result instanceof Magento_Shipping_Model_Rate_Result_Error)) {
            $result = $carrier->proccessAdditionalValidation($request);
        }
        /*
        * Result will be false if the admin set not to show the shipping module
        * if the delivery country is not within specific countries
        */
        if (false !== $result){
            if (!$result instanceof Magento_Shipping_Model_Rate_Result_Error) {
                if ($carrier->getConfigData('shipment_requesttype')) {
                    $packages = $this->composePackagesForCarrier($carrier, $request);
                    if (!empty($packages)) {
                        $sumResults = array();
                        foreach ($packages as $weight => $packageCount) {
                            $request->setPackageWeight($weight);
                            $result = $carrier->collectRates($request);
                            if (!$result) {
                                return $this;
                            } else {
                                $result->updateRatePrice($packageCount);
                            }
                            $sumResults[] = $result;
                        }
                        if (!empty($sumResults) && count($sumResults) > 1) {
                            $result = array();
                            foreach ($sumResults as $res) {
                                if (empty($result)) {
                                    $result = $res;
                                    continue;
                                }
                                foreach ($res->getAllRates() as $method) {
                                    foreach ($result->getAllRates() as $resultMethod) {
                                        if ($method->getMethod() == $resultMethod->getMethod()) {
                                            $resultMethod->setPrice($method->getPrice() + $resultMethod->getPrice());
                                            continue;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $result = $carrier->collectRates($request);
                    }
                } else {
                    $result = $carrier->collectRates($request);
                }
                if (!$result) {
                    return $this;
                }
            }
            if ($carrier->getConfigData('showmethod') == 0 && $result->getError()) {
                return $this;
            }
            // sort rates by price
            if (method_exists($result, 'sortRatesByPrice')) {
                $result->sortRatesByPrice();
            }
            $this->getResult()->append($result);
        }
        return $this;
    }

    /**
     * Compose Packages For Carrier.
     * Devides order into items and items into parts if it's necessary
     *
     * @param Magento_Shipping_Model_Carrier_Abstract $carrier
     * @param Magento_Shipping_Model_Rate_Request $request
     * @return array [int, float]
     */
    public function composePackagesForCarrier($carrier, $request)
    {
        $allItems   = $request->getAllItems();
        $fullItems  = array();

        $maxWeight  = (float) $carrier->getConfigData('max_package_weight');

        foreach ($allItems as $item) {
            if ($item->getProductType() == Magento_Catalog_Model_Product_Type::TYPE_BUNDLE
                && $item->getProduct()->getShipmentType()
            ) {
                continue;
            }

            $qty            = $item->getQty();
            $changeQty      = true;
            $checkWeight    = true;
            $decimalItems   = array();

            if ($item->getParentItem()) {
                if (!$item->getParentItem()->getProduct()->getShipmentType()) {
                    continue;
                }
                $qty = $item->getIsQtyDecimal()
                    ? $item->getParentItem()->getQty()
                    : $item->getParentItem()->getQty() * $item->getQty();
            }

            $itemWeight = $item->getWeight();
            if ($item->getIsQtyDecimal() && $item->getProductType() != Magento_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                $stockItem = $item->getProduct()->getStockItem();
                if ($stockItem->getIsDecimalDivided()) {
                   if ($stockItem->getEnableQtyIncrements() && $stockItem->getQtyIncrements()) {
                        $itemWeight = $itemWeight * $stockItem->getQtyIncrements();
                        $qty        = round(($item->getWeight() / $itemWeight) * $qty);
                        $changeQty  = false;
                   } else {
                       $itemWeight = $itemWeight * $item->getQty();
                       if ($itemWeight > $maxWeight) {
                           $qtyItem = floor($itemWeight / $maxWeight);
                           $decimalItems[] = array('weight' => $maxWeight, 'qty' => $qtyItem);
                           $weightItem = $this->_coreData->getExactDivision($itemWeight, $maxWeight);
                           if ($weightItem) {
                               $decimalItems[] = array('weight' => $weightItem, 'qty' => 1);
                           }
                           $checkWeight = false;
                       } else {
                           $itemWeight = $itemWeight * $item->getQty();
                       }
                   }
                } else {
                    $itemWeight = $itemWeight * $item->getQty();
                }
            }

            if ($checkWeight && $maxWeight && $itemWeight > $maxWeight) {
                return array();
            }

            if ($changeQty && !$item->getParentItem() && $item->getIsQtyDecimal()
                && $item->getProductType() != Magento_Catalog_Model_Product_Type::TYPE_BUNDLE
            ) {
                $qty = 1;
            }

            if (!empty($decimalItems)) {
                foreach ($decimalItems as $decimalItem) {
                    $fullItems = array_merge($fullItems,
                        array_fill(0, $decimalItem['qty'] * $qty, $decimalItem['weight'])
                    );
                }
            } else {
                $fullItems = array_merge($fullItems, array_fill(0, $qty, $itemWeight));
            }
        }
        sort($fullItems);

        return $this->_makePieces($fullItems, $maxWeight);
    }

    /**
     * Make pieces
     * Compose packeges list based on given items, so that each package is as heavy as possible
     *
     * @param array $items
     * @param float $maxWeight
     * @return array
     */
    protected function _makePieces($items, $maxWeight)
    {
        $pieces = array();
        if (!empty($items)) {
            $sumWeight = 0;

            $reverseOrderItems = $items;
            arsort($reverseOrderItems);

            foreach ($reverseOrderItems as $key => $weight) {
                if (!isset($items[$key])) {
                    continue;
                }
                unset($items[$key]);
                $sumWeight = $weight;
                foreach ($items as $key => $weight) {
                    if (($sumWeight + $weight) < $maxWeight) {
                        unset($items[$key]);
                        $sumWeight += $weight;
                    } elseif (($sumWeight + $weight) > $maxWeight) {
                        $pieces[] = (string)(float)$sumWeight;
                        break;
                    } else {
                        unset($items[$key]);
                        $pieces[] = (string)(float)($sumWeight + $weight);
                        $sumWeight = 0;
                        break;
                    }
                }
            }
            if ($sumWeight > 0) {
                $pieces[] = (string)(float)$sumWeight;
            }
            $pieces = array_count_values($pieces);
        }

        return $pieces;
    }

    /**
     * Collect rates by address
     *
     * @param Magento_Object $address
     * @param null|bool|array $limitCarrier
     * @return Magento_Shipping_Model_Shipping
     */
    public function collectRatesByAddress(Magento_Object $address, $limitCarrier = null)
    {
        /** @var $request Magento_Shipping_Model_Rate_Request */
        $request = $this->_rateRequestFactory->create();
        $request->setAllItems($address->getAllItems());
        $request->setDestCountryId($address->getCountryId());
        $request->setDestRegionId($address->getRegionId());
        $request->setDestPostcode($address->getPostcode());
        $request->setPackageValue($address->getBaseSubtotal());
        $request->setPackageValueWithDiscount($address->getBaseSubtotalWithDiscount());
        $request->setPackageWeight($address->getWeight());
        $request->setFreeMethodWeight($address->getFreeMethodWeight());
        $request->setPackageQty($address->getItemQty());
        $request->setStoreId($this->_storeManager->getStore()->getId());
        $request->setWebsiteId($this->_storeManager->getStore()->getWebsiteId());
        $request->setBaseCurrency($this->_storeManager->getStore()->getBaseCurrency());
        $request->setPackageCurrency($this->_storeManager->getStore()->getCurrentCurrency());
        $request->setLimitCarrier($limitCarrier);

        $request->setBaseSubtotalInclTax($address->getBaseSubtotalInclTax());

        return $this->collectRates($request);
    }

    /**
     * Set part of carrier xml config path
     *
     * @param string $code
     * @return Magento_Shipping_Model_Shipping
     */
    public function setCarrierAvailabilityConfigField($code = 'active')
    {
        $this->_availabilityConfigField = $code;
        return $this;
    }

    /**
     * Get carrier by its code
     *
     * @param string $carrierCode
     * @param null|int $storeId
     * @return bool|Magento_Core_Model_Abstract
     */
    public function getCarrierByCode($carrierCode, $storeId = null)
    {
        $isActive = $this->_coreStoreConfig
            ->getConfigFlag('carriers/' . $carrierCode . '/' . $this->_availabilityConfigField, $storeId);
        if (!$isActive) {
            return false;
        }

        return $this->_carrierFactory->create($carrierCode, $storeId);
    }

    /**
     * Prepare and do request to shipment
     *
     * @param Magento_Sales_Model_Order_Shipment $orderShipment
     * @return Magento_Object
     * @throws Magento_Core_Exception
     */
    public function requestToShipment(Magento_Sales_Model_Order_Shipment $orderShipment)
    {
        $admin = $this->_authSession->getUser();
        $order = $orderShipment->getOrder();
        $address = $order->getShippingAddress();
        $shippingMethod = $order->getShippingMethod(true);
        $shipmentStoreId = $orderShipment->getStoreId();
        $shipmentCarrier = $order->getShippingCarrier();
        $baseCurrencyCode = $this->_storeManager->getStore($shipmentStoreId)->getBaseCurrencyCode();
        if (!$shipmentCarrier) {
            throw new Magento_Core_Exception('Invalid carrier: ' . $shippingMethod->getCarrierCode());
        }
        $shipperRegionCode = $this->_coreStoreConfig->getConfig(self::XML_PATH_STORE_REGION_ID, $shipmentStoreId);
        if (is_numeric($shipperRegionCode)) {
            $shipperRegionCode = $this->_regionFactory->create()->load($shipperRegionCode)->getCode();
        }

        $recipientRegionCode = $this->_regionFactory->create()->load($address->getRegionId())->getCode();

        $originStreet1 = $this->_coreStoreConfig->getConfig(self::XML_PATH_STORE_ADDRESS1, $shipmentStoreId);
        $originStreet2 = $this->_coreStoreConfig->getConfig(self::XML_PATH_STORE_ADDRESS2, $shipmentStoreId);
        $storeInfo = new Magento_Object($this->_coreStoreConfig->getConfig('general/store_information', $shipmentStoreId));

        if (!$admin->getFirstname() || !$admin->getLastname() || !$storeInfo->getName() || !$storeInfo->getPhone()
            || !$originStreet1 || !$this->_coreStoreConfig->getConfig(self::XML_PATH_STORE_CITY, $shipmentStoreId)
            || !$shipperRegionCode || !$this->_coreStoreConfig->getConfig(self::XML_PATH_STORE_ZIP, $shipmentStoreId)
            || !$this->_coreStoreConfig->getConfig(self::XML_PATH_STORE_COUNTRY_ID, $shipmentStoreId)
        ) {
            throw new Magento_Core_Exception(
                __('We don\'t have enough information to create shipping labels. Please make sure your store information and settings are complete.')
            );
        }

        /** @var $request Magento_Shipping_Model_Shipment_Request */
        $request = $this->_rateRequestFactory->create();
        $request->setOrderShipment($orderShipment);
        $request->setShipperContactPersonName($admin->getName());
        $request->setShipperContactPersonFirstName($admin->getFirstname());
        $request->setShipperContactPersonLastName($admin->getLastname());
        $request->setShipperContactCompanyName($storeInfo->getName());
        $request->setShipperContactPhoneNumber($storeInfo->getPhone());
        $request->setShipperEmail($admin->getEmail());
        $request->setShipperAddressStreet(trim($originStreet1 . ' ' . $originStreet2));
        $request->setShipperAddressStreet1($originStreet1);
        $request->setShipperAddressStreet2($originStreet2);
        $request->setShipperAddressCity($this->_coreStoreConfig->getConfig(self::XML_PATH_STORE_CITY, $shipmentStoreId));
        $request->setShipperAddressStateOrProvinceCode($shipperRegionCode);
        $request->setShipperAddressPostalCode($this->_coreStoreConfig->getConfig(self::XML_PATH_STORE_ZIP, $shipmentStoreId));
        $request->setShipperAddressCountryCode($this->_coreStoreConfig->getConfig(self::XML_PATH_STORE_COUNTRY_ID, $shipmentStoreId));
        $request->setRecipientContactPersonName(trim($address->getFirstname() . ' ' . $address->getLastname()));
        $request->setRecipientContactPersonFirstName($address->getFirstname());
        $request->setRecipientContactPersonLastName($address->getLastname());
        $request->setRecipientContactCompanyName($address->getCompany());
        $request->setRecipientContactPhoneNumber($address->getTelephone());
        $request->setRecipientEmail($address->getEmail());
        $request->setRecipientAddressStreet(trim($address->getStreet1() . ' ' . $address->getStreet2()));
        $request->setRecipientAddressStreet1($address->getStreet1());
        $request->setRecipientAddressStreet2($address->getStreet2());
        $request->setRecipientAddressCity($address->getCity());
        $request->setRecipientAddressStateOrProvinceCode($address->getRegionCode());
        $request->setRecipientAddressRegionCode($recipientRegionCode);
        $request->setRecipientAddressPostalCode($address->getPostcode());
        $request->setRecipientAddressCountryCode($address->getCountryId());
        $request->setShippingMethod($shippingMethod->getMethod());
        $request->setPackageWeight($order->getWeight());
        $request->setPackages($orderShipment->getPackages());
        $request->setBaseCurrencyCode($baseCurrencyCode);
        $request->setStoreId($shipmentStoreId);

        return $shipmentCarrier->requestToShipment($request);
    }
}
