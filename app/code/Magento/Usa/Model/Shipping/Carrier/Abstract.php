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
 * Abstract USA shipping carrier model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Usa_Model_Shipping_Carrier_Abstract extends Magento_Shipping_Model_Carrier_Abstract
{

    const USA_COUNTRY_ID = 'US';
    const PUERTORICO_COUNTRY_ID = 'PR';
    const GUAM_COUNTRY_ID = 'GU';
    const GUAM_REGION_CODE = 'GU';

    protected static $_quotesCache = array();

    /**
     * Flag for check carriers for activity
     *
     * @var string
     */
    protected $_activeFlag = 'active';

    /**
     * Directory data
     *
     * @var Magento_Directory_Helper_Data
     */
    protected $_directoryData = null;

    /**
     * @var Magento_Usa_Model_Simplexml_ElementFactory
     */
    protected $_xmlElFactory;

    /**
     * @var Magento_Shipping_Model_Rate_ResultFactory
     */
    protected $_rateFactory;

    /**
     * @var Magento_Shipping_Model_Rate_Result_MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var Magento_Shipping_Model_Rate_Result_ErrorFactory
     */
    protected $_rateErrorFactory;

    /**
     * @var Magento_Shipping_Model_Tracking_ResultFactory
     */
    protected $_trackFactory;

    /**
     * @var Magento_Shipping_Model_Tracking_Result_ErrorFactory
     */
    protected $_trackErrorFactory;

    /**
     * @var Magento_Shipping_Model_Tracking_Result_StatusFactory
     */
    protected $_trackStatusFactory;

    /**
     * @var Magento_Directory_Model_RegionFactory
     */
    protected $_regionFactory;

    /**
     * @var Magento_Directory_Model_CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var Magento_Directory_Model_CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param Magento_Usa_Model_Simplexml_ElementFactory $xmlElFactory
     * @param Magento_Shipping_Model_Rate_ResultFactory $rateFactory
     * @param Magento_Shipping_Model_Rate_Result_MethodFactory $rateMethodFactory
     * @param Magento_Shipping_Model_Rate_Result_ErrorFactory $rateErrorFactory
     * @param Magento_Shipping_Model_Tracking_ResultFactory $trackFactory
     * @param Magento_Shipping_Model_Tracking_Result_ErrorFactory $trackErrorFactory
     * @param Magento_Shipping_Model_Tracking_Result_StatusFactory $trackStatusFactory
     * @param Magento_Directory_Model_RegionFactory $regionFactory
     * @param Magento_Directory_Model_CountryFactory $countryFactory
     * @param Magento_Directory_Model_CurrencyFactory $currencyFactory
     * @param Magento_Directory_Helper_Data $directoryData
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Usa_Model_Simplexml_ElementFactory $xmlElFactory,
        Magento_Shipping_Model_Rate_ResultFactory $rateFactory,
        Magento_Shipping_Model_Rate_Result_MethodFactory $rateMethodFactory,
        Magento_Shipping_Model_Rate_Result_ErrorFactory $rateErrorFactory,
        Magento_Shipping_Model_Tracking_ResultFactory $trackFactory,
        Magento_Shipping_Model_Tracking_Result_ErrorFactory $trackErrorFactory,
        Magento_Shipping_Model_Tracking_Result_StatusFactory $trackStatusFactory,
        Magento_Directory_Model_RegionFactory $regionFactory,
        Magento_Directory_Model_CountryFactory $countryFactory,
        Magento_Directory_Model_CurrencyFactory $currencyFactory,
        Magento_Directory_Helper_Data $directoryData,
        array $data = array()
    ) {
        $this->_xmlElFactory = $xmlElFactory;
        $this->_rateFactory = $rateFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_rateErrorFactory = $rateErrorFactory;
        $this->_trackFactory = $trackFactory;
        $this->_trackErrorFactory = $trackErrorFactory;
        $this->_trackStatusFactory = $trackStatusFactory;
        $this->_regionFactory = $regionFactory;
        $this->_countryFactory = $countryFactory;
        $this->_currencyFactory = $currencyFactory;
        $this->_directoryData = $directoryData;
        parent::__construct($data);
    }

    /**
     * Set flag for check carriers for activity
     *
     * @param string $code
     * @return Magento_Usa_Model_Shipping_Carrier_Abstract
     */
    public function setActiveFlag($code = 'active')
    {
        $this->_activeFlag = $code;
        return $this;
    }

    /**
     * Return code of carrier
     *
     * @return string
     */
    public function getCarrierCode()
    {
        return isset($this->_code) ? $this->_code : null;
    }

    public function getTrackingInfo($tracking)
    {
        $info = array();

        $result = $this->getTracking($tracking);

        if($result instanceof Magento_Shipping_Model_Tracking_Result){
            if ($trackings = $result->getAllTrackings()) {
                return $trackings[0];
            }
        }
        elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        return false;
    }

    /**
     * Check if carrier has shipping tracking option available
     * All Magento_Usa carriers have shipping tracking option available
     *
     * @return boolean
     */
    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * Check if city option required
     *
     * @return boolean
     */
    public function isCityRequired()
    {
        return true;
    }

    /**
     * Determine whether zip-code is required for the country of destination
     *
     * @param string|null $countryId
     * @return bool
     */
    public function isZipCodeRequired($countryId = null)
    {
        if ($countryId != null) {
            return !$this->_directoryData->isZipCodeOptional($countryId);
        }
        return true;
    }

    /**
     * Check if carrier has shipping label option available
     *
     * @return boolean
     */
    public function isShippingLabelsAvailable()
    {
        return true;
    }

    /**
     * Return items for further shipment rate evaluation. We need to pass children of a bundle instead passing the
     * bundle itself, otherwise we may not get a rate at all (e.g. when total weight of a bundle exceeds max weight
     * despite each item by itself is not)
     *
     * @param Magento_Shipping_Model_Rate_Request $request
     * @return array
     */
    public function getAllItems(Magento_Shipping_Model_Rate_Request $request)
    {
        $items = array();
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                /* @var $item Magento_Sales_Model_Quote_Item */
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    // Don't process children here - we will process (or already have processed) them below
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if (!$child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $items[] = $child;
                        }
                    }
                } else {
                    // Ship together - count compound item as one solid
                    $items[] = $item;
                }
            }
        }
        return $items;
    }

    /**
     * Processing additional validation to check if carrier applicable.
     *
     * @param Magento_Shipping_Model_Rate_Request $request
     * @return Magento_Shipping_Model_Carrier_Abstract|Magento_Shipping_Model_Rate_Result_Error|boolean
     */
    public function proccessAdditionalValidation(Magento_Shipping_Model_Rate_Request $request)
    {
        //Skip by item validation if there is no items in request
        if(!count($this->getAllItems($request))) {
            return $this;
        }

        $maxAllowedWeight   = (float) $this->getConfigData('max_package_weight');
        $errorMsg           = '';
        $configErrorMsg     = $this->getConfigData('specificerrmsg');
        $defaultErrorMsg    = __('The shipping module is not available.');
        $showMethod         = $this->getConfigData('showmethod');

        foreach ($this->getAllItems($request) as $item) {
            if ($item->getProduct() && $item->getProduct()->getId()) {
                $weight         = $item->getProduct()->getWeight();
                $stockItem      = $item->getProduct()->getStockItem();
                $doValidation   = true;

                if ($stockItem->getIsQtyDecimal() && $stockItem->getIsDecimalDivided()) {
                    if ($stockItem->getEnableQtyIncrements() && $stockItem->getQtyIncrements()) {
                        $weight = $weight * $stockItem->getQtyIncrements();
                    } else {
                        $doValidation = false;
                    }
                } elseif ($stockItem->getIsQtyDecimal() && !$stockItem->getIsDecimalDivided()) {
                    $weight = $weight * $item->getQty();
                }

                if ($doValidation && $weight > $maxAllowedWeight) {
                    $errorMsg = ($configErrorMsg) ? $configErrorMsg : $defaultErrorMsg;
                    break;
                }
            }
        }

        if (!$errorMsg && !$request->getDestPostcode() && $this->isZipCodeRequired($request->getDestCountryId())) {
            $errorMsg = __('This shipping method is not available. Please specify the zip code.');
        }

        if ($errorMsg && $showMethod) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($errorMsg);
            return $error;
        } elseif ($errorMsg) {
            return false;
        }
        return $this;
    }

    /**
     * Returns cache key for some request to carrier quotes service
     *
     * @param string|array $requestParams
     * @return string
     */
    protected function _getQuotesCacheKey($requestParams)
    {
        if (is_array($requestParams)) {
            $requestParams = implode(',', array_merge(
                array($this->getCarrierCode()),
                array_keys($requestParams),
                $requestParams)
            );
        }
        return crc32($requestParams);
    }

    /**
     * Checks whether some request to rates have already been done, so we have cache for it
     * Used to reduce number of same requests done to carrier service during one session
     *
     * Returns cached response or null
     *
     * @param string|array $requestParams
     * @return null|string
     */
    protected function _getCachedQuotes($requestParams)
    {
        $key = $this->_getQuotesCacheKey($requestParams);
        return isset(self::$_quotesCache[$key]) ? self::$_quotesCache[$key] : null;
    }

    /**
     * Sets received carrier quotes to cache
     *
     * @param string|array $requestParams
     * @param string $response
     * @return Magento_Usa_Model_Shipping_Carrier_Abstract
     */
    protected function _setCachedQuotes($requestParams, $response)
    {
        $key = $this->_getQuotesCacheKey($requestParams);
        self::$_quotesCache[$key] = $response;
        return $this;
    }

    /**
     * Prepare service name. Strip tags and entities from name
     *
     * @param string|object $name  service name or object with implemented __toString() method
     * @return string              prepared service name
     */
    protected function _prepareServiceName($name)
    {
        $name = html_entity_decode((string)$name);
        $name = strip_tags(preg_replace('#&\w+;#', '', $name));
        return trim($name);
    }

    /**
     * Prepare shipment request.
     * Validate and correct request information
     *
     * @param Magento_Object $request
     *
     */
    protected function _prepareShipmentRequest(Magento_Object $request)
    {
        $phonePattern = '/[\s\_\-\(\)]+/';
        $phoneNumber = $request->getShipperContactPhoneNumber();
        $phoneNumber = preg_replace($phonePattern, '', $phoneNumber);
        $request->setShipperContactPhoneNumber($phoneNumber);
        $phoneNumber = $request->getRecipientContactPhoneNumber();
        $phoneNumber = preg_replace($phonePattern, '', $phoneNumber);
        $request->setRecipientContactPhoneNumber($phoneNumber);
    }

    /**
     * Do request to shipment
     *
     * @param Magento_Shipping_Model_Shipment_Request $request
     * @return array
     */
    public function requestToShipment(Magento_Shipping_Model_Shipment_Request $request)
    {
        $packages = $request->getPackages();
        if (!is_array($packages) || !$packages) {
            throw new Magento_Core_Exception(__('No packages for request'));
        }
        if ($request->getStoreId() != null) {
            $this->setStore($request->getStoreId());
        }
        $data = array();
        foreach ($packages as $packageId => $package) {
            $request->setPackageId($packageId);
            $request->setPackagingType($package['params']['container']);
            $request->setPackageWeight($package['params']['weight']);
            $request->setPackageParams(new Magento_Object($package['params']));
            $request->setPackageItems($package['items']);
            $result = $this->_doShipmentRequest($request);

            if ($result->hasErrors()) {
                $this->rollBack($data);
                break;
            } else {
                $data[] = array(
                    'tracking_number' => $result->getTrackingNumber(),
                    'label_content'   => $result->getShippingLabelContent()
                );
            }
            if (!isset($isFirstRequest)) {
                $request->setMasterTrackingId($result->getTrackingNumber());
                $isFirstRequest = false;
            }
        }

        $response = new Magento_Object(array(
            'info'   => $data
        ));
        if ($result->getErrors()) {
            $response->setErrors($result->getErrors());
        }
        return $response;
    }

    /**
     * Do request to RMA shipment
     *
     * @param $request
     * @return array
     */
    public function returnOfShipment($request)
    {
        $request->setIsReturn(true);
        $packages = $request->getPackages();
        if (!is_array($packages) || !$packages) {
            throw new Magento_Core_Exception(__('No packages for request'));
        }
        if ($request->getStoreId() != null) {
            $this->setStore($request->getStoreId());
        }
        $data = array();
        foreach ($packages as $packageId => $package) {
            $request->setPackageId($packageId);
            $request->setPackagingType($package['params']['container']);
            $request->setPackageWeight($package['params']['weight']);
            $request->setPackageParams(new Magento_Object($package['params']));
            $request->setPackageItems($package['items']);
            $result = $this->_doShipmentRequest($request);

            if ($result->hasErrors()) {
                $this->rollBack($data);
                break;
            } else {
                $data[] = array(
                    'tracking_number' => $result->getTrackingNumber(),
                    'label_content'   => $result->getShippingLabelContent()
                );
            }
            if (!isset($isFirstRequest)) {
                $request->setMasterTrackingId($result->getTrackingNumber());
                $isFirstRequest = false;
            }
        }

        $response = new Magento_Object(array(
            'info'   => $data
        ));
        if ($result->getErrors()) {
            $response->setErrors($result->getErrors());
        }
        return $response;
    }

    /**
     * For multi package shipments. Delete requested shipments if the current shipment
     * request is failed
     *
     * @todo implement rollback logic
     * @param array $data
     * @return bool
     */
    public function rollBack($data)
    {
        return true;
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param Magento_Object $request
     * @return Magento_Object
     */
    abstract protected function _doShipmentRequest(Magento_Object $request);

    /**
     * Check is Country U.S. Possessions and Trust Territories
     *
     * @param string $countyId
     * @return boolean
     */
    protected function _isUSCountry($countyId)
    {
        switch ($countyId) {
            case 'AS': // Samoa American
            case 'GU': // Guam
            case 'MP': // Northern Mariana Islands
            case 'PW': // Palau
            case 'PR': // Puerto Rico
            case 'VI': // Virgin Islands US
            case 'US'; // United States
                return true;
        }

        return false;
    }

    /**
     * Check whether girth is allowed for the carrier
     *
     * @param null|string $countyDest
     * @return bool
     */
    public function isGirthAllowed($countyDest = null) {
        return false;
    }
}
