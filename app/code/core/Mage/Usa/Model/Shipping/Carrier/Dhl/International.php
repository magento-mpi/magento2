<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DHL International (API v1.4)
 *
 * @category Mage
 * @package  Mage_Usa
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl_International
    extends Mage_Usa_Model_Shipping_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Carrier Product indicator
     */
    const DHL_CONTENT_TYPE_DOC        = 'D';
    const DHL_CONTENT_TYPE_NON_DOC    = 'N';

    /**
     * Container types that could be customized
     *
     * @var array
     */
    protected $_customizableContainerTypes = array(self::DHL_CONTENT_TYPE_NON_DOC);

    /**
     * Code of the carrier
     */
    const CODE = 'dhlint';

    /**
     * Rate request data
     *
     * @var Mage_Shipping_Model_Rate_Request|null
     */
    protected $_request = null;

    /**
     * Raw rate request data
     *
     * @var Varien_Object|null
     */
    protected $_rawRequest = null;

    /**
     * Rate result data
     *
     * @var Mage_Shipping_Model_Rate_Result|null
     */
    protected $_result = null;

    /**
     * Countries parameters data
     *
     * @var SimpleXMLElement|null
     */
    protected $_countryParams = null;

    /**
     * Errors placeholder
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Dhl rates result
     *
     * @var array
     */
    protected $_rates = array();

    /**
     * Store Id
     *
     * @var int|null
     */
    protected $_storeId = null;

    /**
     * Carrier's code
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Free Method config path
     *
     * @var string
     */
    protected $_freeMethod = 'free_method_nondoc';

    /**
     * Max weight without fee
     *
     * @var int
     */
    protected $_maxWeight = 70;

    /**
     * Request variables array
     *
     * @var array
     */
    protected $_requestVariables = array(
        'id'                => array('code' => 'dhl_id',                'setCode' => 'id'),
        'password'          => array('code' => 'dhl_password',          'setCode' => 'password'),
        'account'           => array('code' => 'dhl_account',           'setCode' => 'account_nbr'),
        'shipping_key'      => array('code' => 'dhl_shipping_key',      'setCode' => 'shipping_key'),
        'shipping_intlkey'  => array('code' => 'dhl_shipping_intl_key', 'setCode' => 'shipping_intl_key'),
        'shipment_type'     => array('code' => 'dhl_shipment_type',     'setCode' => 'shipment_type'),
        'dutiable'          => array('code' => 'dhl_dutiable',          'setCode' => 'dutiable'),
        'dutypaymenttype'   => array('code' => 'dhl_duty_payment_type', 'setCode' => 'duty_payment_type'),
        'contentdesc'       => array('code' => 'dhl_content_desc',      'setCode' => 'content_desc')
    );

    /**
     * Returns value of given variable
     *
     * @param mixed $origValue
     * @param string $pathToValue
     * @return mixed
     */
    protected function _getDefaultValue($origValue, $pathToValue)
    {
        if (!$origValue) {
            $origValue = Mage::getStoreConfig(
                $pathToValue,
                $this->_storeId
            );
        }

        return $origValue;
    }

    /**
     * Collect and get rates
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool|Mage_Shipping_Model_Rate_Result|null
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag($this->_activeFlag)) {
            return false;
        }

        $requestDhl     = clone $request;
        $this->_storeId  = $requestDhl->getStoreId();

        $origCompanyName = $this->_getDefaultValue(
            $requestDhl->getOrigCompanyName(),
            Mage_Core_Model_Store::XML_PATH_STORE_STORE_NAME
        );
        $origCountryId = $this->_getDefaultValue(
            $requestDhl->getOrigCountryId(),
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID
        );
        $origState = $this->_getDefaultValue(
            $requestDhl->getOrigState(),
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_REGION_ID
        );
        $origCity = $this->_getDefaultValue(
            $requestDhl->getOrigCity(),
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_CITY
        );
        $origPostcode = $this->_getDefaultValue(
            $requestDhl->getOrigPostcode(),
            Mage_Shipping_Model_Shipping::XML_PATH_STORE_ZIP
        );

        $requestDhl->setOrigCompanyName($origCompanyName)
            ->setCountryId($origCountryId)
            ->setOrigState($origState)
            ->setOrigCity($origCity)
            ->setOrigPostal($origPostcode);
        $this->setRequest($requestDhl);

        $this->_result = $this->_getQuotes();

        $this->_updateFreeMethodQuote($request);

        return $this->_result;
    }

    /**
     * Update Free Method Quote
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return void
     */
    protected function _updateFreeMethodQuote($request)
    {
        if ($this->getConfigData('content_type') == self::DHL_CONTENT_TYPE_DOC) {
            $this->_freeMethod = 'free_method_doc';
        }

        parent::_updateFreeMethodQuote($request);
    }

    /**
     * Set Free Method Request
     *
     * @param  string $freeMethod
     * @return void
     */
    protected function _setFreeMethodRequest($freeMethod)
    {
        $rawRequest = $this->_rawRequest;

        $rawRequest->setFreeMethodRequest(true);
        $freeWeight = $this->getTotalNumOfBoxes($rawRequest->getFreeMethodWeight());
        $rawRequest->setWeight($freeWeight);
        $rawRequest->setService($freeMethod);
    }

    /**
     * Returns request result
     *
     * @return Mage_Shipping_Model_Rate_Result|null
     */
    public function getResult()
    {
        return $this->_result;
    }

    protected function _addParams($requestObject)
    {
        $request = $this->_request;
        foreach ($this->_requestVariables as $code => $objectCode) {
            if ($request->getDhlId()) {
                $value = $request->getData($objectCode['code']);
            } else {
                $value = $this->getConfigData($code);
            }
            $requestObject->setData($objectCode['setCode'], $value);
        }
        return $requestObject;
    }

    /**
     * Prepare and set request in property of current instance
     *
     * @param Varien_Object $request
     * @return Mage_Usa_Model_Shipping_Carrier_Dhl
     */
    public function setRequest(Varien_Object $request)
    {
        $this->_request = $request;
        $this->_storeId = $request->getStoreId();

        $requestObject = new Varien_Object();

        $requestObject->setIsGenerateLabelReturn($request->getIsGenerateLabelReturn());

        $requestObject->setStoreId($request->getStoreId());

        if ($request->getLimitMethod()) {
            $requestObject->setService($request->getLimitMethod());
        }

        $requestObject = $this->_addParams($requestObject);

        if ($request->getDestPostcode()) {
            $requestObject->setDestPostal($request->getDestPostcode());
        }

        $requestObject->setOrigCountry(
                $this->_getDefaultValue(
                    $request->getOrigCountry(), Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID)
            )
            ->setOrigCountryId(
                $this->_getDefaultValue(
                    $request->getOrigCountryId(), Mage_Shipping_Model_Shipping::XML_PATH_STORE_COUNTRY_ID)
            );

        $shippingWeight = $request->getPackageWeight();

        $requestObject->setValue(round($request->getPackageValue(), 2))
            ->setValueWithDiscount($request->getPackageValueWithDiscount())
            ->setCustomsValue($request->getPackageCustomsValue())
            ->setDestStreet(
                Mage::helper('core/string')->substr(str_replace("\n", '', $request->getDestStreet()), 0, 35))
            ->setDestStreetLine2($request->getDestStreetLine2())
            ->setDestCity($request->getDestCity())
            ->setOrigCompanyName($request->getOrigCompanyName())
            ->setOrigCity($request->getOrigCity())
            ->setOrigPhoneNumber($request->getOrigPhoneNumber())
            ->setOrigPersonName($request->getOrigPersonName())
            ->setOrigEmail(Mage::getStoreConfig('trans_email/ident_general/email', $requestObject->getStoreId()))
            ->setOrigCity($request->getOrigCity())
            ->setOrigPostal($request->getOrigPostal())
            ->setOrigStreetLine2($request->getOrigStreetLine2())
            ->setDestPhoneNumber($request->getDestPhoneNumber())
            ->setDestPersonName($request->getDestPersonName())
            ->setDestCompanyName($request->getDestCompanyName());

        $originStreet2 = Mage::getStoreConfig(
                Mage_Shipping_Model_Shipping::XML_PATH_STORE_ADDRESS2, $requestObject->getStoreId());

        $requestObject->setOrigStreet($request->getOrigStreet() ? $request->getOrigStreet() : $originStreet2);

        if (is_numeric($request->getOrigState())) {
            $requestObject->setOrigState(Mage::getModel('directory/region')->load($request->getOrigState())->getCode());
        } else {
            $requestObject->setOrigState($request->getOrigState());
        }

        if ($request->getDestCountryId()) {
            $destCountry = $request->getDestCountryId();
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }

        // for DHL, Puerto Rico state for US will assume as Puerto Rico country
        // for Puerto Rico, dhl will ship as international
        if ($destCountry == self::USA_COUNTRY_ID && ($request->getDestPostcode() == '00912'
                                                     || $request->getDestRegionCode() == self::PUERTORICO_COUNTRY_ID)
        ) {
            $destCountry = self::PUERTORICO_COUNTRY_ID;
        }

        $requestObject->setDestCountryId($destCountry)
            ->setDestState($request->getDestRegionCode())
            ->setWeight($shippingWeight)
            ->setFreeMethodWeight($request->getFreeMethodWeight())
            ->setOrderShipment($request->getOrderShipment());

        if ($request->getPackageId()) {
            $requestObject->setPackageId($request->getPackageId());
        }

        $requestObject->setBaseSubtotalInclTax($request->getBaseSubtotalInclTax());

        $this->_rawRequest = $requestObject;
        return $this;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $contentType = $this->getConfigData('content_type');
        $allowedMethods = array();
        switch ($contentType) {
            case self::DHL_CONTENT_TYPE_DOC:
                $allowedMethods = explode(',', $this->getConfigData('doc_methods'));
                break;

            case self::DHL_CONTENT_TYPE_NON_DOC:
                $allowedMethods = explode(',', $this->getConfigData('nondoc_methods'));
                break;
            default:
                Mage::throwException(Mage::helper('usa')->__('Wrong Content Type.'));
        }
        $methods = array();
        foreach ($allowedMethods as $method) {
            $methods[$method] = $this->getDhlProductTitle($method);
        }
        return $methods;
    }

    /**
     * Get configuration data of carrier
     *
     * @param strin $type
     * @param string $code
     * @return array|bool
     */
    public function getCode($type, $code = '')
    {
        $codes = array(
            'unit_of_measure'   => array(
                'L' => Mage::helper('usa')->__('Pounds'),
                'K' => Mage::helper('usa')->__('Kilograms'),
            ),
            'unit_of_dimension' => array(
                'I' => Mage::helper('usa')->__('Inches'),
                'C' => Mage::helper('usa')->__('Centimeters'),
            ),
            'unit_of_dimension_cut' => array(
                'I' => Mage::helper('usa')->__('inch'),
                'C' => Mage::helper('usa')->__('cm'),
            ),
            'dimensions' => array(
                'height'    => Mage::helper('usa')->__('Height'),
                'depth'     => Mage::helper('usa')->__('Depth'),
                'width'     => Mage::helper('usa')->__('Width'),
            ),
            'size'              => array(
                '0' => Mage::helper('usa')->__('Regular'),
                '1' => Mage::helper('usa')->__('Specific'),
            ),
            'dimensions_variables'  => array(
                'L'         => Zend_Measure_Weight::POUND,
                'LB'        => Zend_Measure_Weight::POUND,
                'POUND'     => Zend_Measure_Weight::POUND,
                'K'         => Zend_Measure_Weight::KILOGRAM,
                'KG'        => Zend_Measure_Weight::KILOGRAM,
                'KILOGRAM'  => Zend_Measure_Weight::KILOGRAM,
                'I'         => Zend_Measure_Length::INCH,
                'IN'        => Zend_Measure_Length::INCH,
                'INCH'      => Zend_Measure_Length::INCH,
                'C'         => Zend_Measure_Length::CENTIMETER,
                'CM'        => Zend_Measure_Length::CENTIMETER,
                'CENTIMETER'=> Zend_Measure_Length::CENTIMETER,

            )
        );

        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    /**
     * Returns DHL shipment methods (depending on package content type, if necessary)
     *
     * @param string $doc Package content type (doc/non-doc) see DHL_CONTENT_TYPE_* constants
     * @return array
     */
    public function getDhlProducts($doc)
    {
        if ($doc == self::DHL_CONTENT_TYPE_DOC) {
            // Documents shipping
            return array(
                '2' => Mage::helper('usa')->__('Easy shop'),
                '5' => Mage::helper('usa')->__('Sprintline'),
                '6' => Mage::helper('usa')->__('Secureline'),
                '7' => Mage::helper('usa')->__('Express easy'),
                '9' => Mage::helper('usa')->__('Europack'),
                'B' => Mage::helper('usa')->__('Break bulk express'),
                'C' => Mage::helper('usa')->__('Medical express'),
                'D' => Mage::helper('usa')->__('Express worldwide'), // product content code: DOX
                'U' => Mage::helper('usa')->__('Express worldwide'), // product content code: ECX
                'K' => Mage::helper('usa')->__('Express 9:00'),
                'L' => Mage::helper('usa')->__('Express 10:30'),
                'G' => Mage::helper('usa')->__('Domestic economy select'),
                'W' => Mage::helper('usa')->__('Economy select'),
                'I' => Mage::helper('usa')->__('Break bulk economy'),
                'N' => Mage::helper('usa')->__('Domestic express'),
                'O' => Mage::helper('usa')->__('Others'),
                'R' => Mage::helper('usa')->__('Globalmail business'),
                'S' => Mage::helper('usa')->__('Same day'),
                'T' => Mage::helper('usa')->__('Express 12:00'),
                'X' => Mage::helper('usa')->__('Express envelope'),
            );
        } else {
            // Services for shipping non-documents cargo
            return array(
                '1' => Mage::helper('usa')->__('Customer services'),
                '3' => Mage::helper('usa')->__('Easy shop'),
                '4' => Mage::helper('usa')->__('Jetline'),
                '8' => Mage::helper('usa')->__('Express easy'),
                'P' => Mage::helper('usa')->__('Express worldwide'),
                'Q' => Mage::helper('usa')->__('Medical express'),
                'E' => Mage::helper('usa')->__('Express 9:00'),
                'F' => Mage::helper('usa')->__('Freight worldwide'),
                'H' => Mage::helper('usa')->__('Economy select'),
                'J' => Mage::helper('usa')->__('Jumbo box'),
                'M' => Mage::helper('usa')->__('Express 10:30'),
                'V' => Mage::helper('usa')->__('Europack'),
                'Y' => Mage::helper('usa')->__('Express 12:00'),
            );
        }
    }

    /**
     * Returns title of DHL shipping method by its code
     *
     * @param string $code One-symbol code (see getDhlProducts())
     * @return bool
     */
    public function getDhlProductTitle($code)
    {
        $contentType = $this->getConfigData('content_type');
        $dhlProducts = $this->getDhlProducts($contentType);
        return isset($dhlProducts[$code]) ? $dhlProducts[$code] : false;
    }

    /**
     * Convert item weight to needed weight based on config weight unit dimensions
     *
     * @param float $weight
     * @param bool $maxWeight
     * @return float
     */
    protected function _getWeight($weight, $maxWeight = false)
    {
        if ($maxWeight) {
            $configWeightUnit = Zend_Measure_Weight::KILOGRAM;
        } else {
            $configWeightUnit = $this->getCode(
                'dimensions_variables',
                strtoupper((string)$this->getConfigData('unit_of_measure'))
            );
        }

        $countryWeightUnit = $this->getCode(
            'dimensions_variables',
            strtoupper($this->_getWeightUnit())
        );

        if ($configWeightUnit != $countryWeightUnit) {
            $weight = round(Mage::helper('usa')->convertMeasureWeight(
                $weight,
                $configWeightUnit,
                $countryWeightUnit
            ), 3);
        }

        return round($weight, 3);
    }

    /**
     * Prepare items to pieces
     *
     * @return array
     */
    protected function _getAllItems()
    {
        $allItems   = $this->_request->getAllItems();
        $fullItems  = array();
        foreach ($allItems as $item) {
            if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
                && $item->getProduct()->getShipmentType()
            ) {
                continue;
            }

            $qty = $item->getQty();

            if ($item->getParentItem()) {
                if (!$item->getParentItem()->getProduct()->getShipmentType()) {
                    continue;
                }
                $qty = $item->getIsQtyDecimal()
                    ? $item->getParentItem()->getQty()
                    : $item->getParentItem()->getQty() * $item->getQty();
            }

            $itemWeight = $item->getIsQtyDecimal() ? $item->getWeight() * $item->getQty() : $item->getWeight();

            if ($this->_getWeight($itemWeight) > $this->_getWeight($this->_maxWeight, true)) {
                return array();
            }

            if (!$item->getParentItem() && $item->getIsQtyDecimal()) {
                $qty = 1;
            }
            $fullItems = array_merge($fullItems, array_fill(0, $qty, $itemWeight));
        }
        sort($fullItems);

        return $fullItems;
    }

    /**
     * Make pieces
     *
     * @param SimpleXMLElement $nodeBkgDetails
     * @return void
     */
    protected function _makePieces(SimpleXMLElement $nodeBkgDetails)
    {
        $divideOrderWeight = (string)$this->getConfigData('divide_order_weight');
        $nodePieces = $nodeBkgDetails->addChild('Pieces', '', '');
        $items = $this->_getAllItems();

        if ($divideOrderWeight && !empty($items)) {
            $maxWeight = $this->_getWeight($this->_maxWeight, true);
            $sumWeight = 0;
            $numberOfPieces = 0;

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
                        $numberOfPieces++;
                        $nodePiece = $nodePieces->addChild('Piece', '', '');
                        $nodePiece->addChild('PieceID', $numberOfPieces);
                        $this->_addDimension($nodePiece);
                        $nodePiece->addChild('Weight', $sumWeight);
                        break;
                    } else {
                        unset($items[$key]);
                        $numberOfPieces++;
                        $sumWeight += $weight;
                        $nodePiece = $nodePieces->addChild('Piece', '', '');
                        $nodePiece->addChild('PieceID', $numberOfPieces);
                        $this->_addDimension($nodePiece);
                        $nodePiece->addChild('Weight', $sumWeight);
                        $sumWeight = 0;
                        break;
                    }
                }
            }
            if ($sumWeight > 0) {
                $numberOfPieces++;
                $nodePiece = $nodePieces->addChild('Piece', '', '');
                $nodePiece->addChild('PieceID', $numberOfPieces);
                $this->_addDimension($nodePiece);
                $nodePiece->addChild('Weight', $sumWeight);
            }
        } else {
            $nodePiece = $nodePieces->addChild('Piece', '', '');
            $nodePiece->addChild('PieceID', 1);
            $this->_addDimension($nodePiece);
            $nodePiece->addChild('Weight', $this->_getWeight($this->_rawRequest->getWeight()));
        }
    }

    /**
     * Convert item dimension to needed dimension based on config dimension unit of measure
     *
     * @param float $dimension
     * @return float
     */
    protected function _getDimension($dimension)
    {
        $configWeightUnit = $this->getCode(
            'dimensions_variables',
            strtoupper((string)$this->getConfigData('unit_of_measure'))
        );

        if ($configWeightUnit == Zend_Measure_Weight::POUND) {
            $configWeightUnit = Zend_Measure_Length::INCH;
        } else {
            $configWeightUnit = Zend_Measure_Length::CENTIMETER;
        }


        $countryDimensionUnit = $this->getCode(
            'dimensions_variables',
            strtoupper($this->_getDimensionUnit())
        );

        if ($configWeightUnit != $countryDimensionUnit) {
            $dimension = round(Mage::helper('usa')->convertMeasureDimension(
                $dimension,
                $configWeightUnit,
                $countryDimensionUnit
            ), 3);
        }

        return round($dimension, 3);
    }

    /**
     * Add dimension to piece
     *
     * @param SimpleXMLElement $nodePiece
     * @return void
     */
    protected function _addDimension($nodePiece)
    {
        $sizeChecker = (string)$this->getConfigData('size');

        $height = $this->_getDimension((string)$this->getConfigData('height'));
        $depth = $this->_getDimension((string)$this->getConfigData('depth'));
        $width = $this->_getDimension((string)$this->getConfigData('width'));

        if ($sizeChecker && $height && $depth && $width) {
            $nodePiece->addChild('Height', $height);
            $nodePiece->addChild('Depth', $depth);
            $nodePiece->addChild('Width', $width);
        }
    }

    /**
     * Get shipping quotes
     *
     * @return Mage_Core_Model_Abstract|Mage_Shipping_Model_Rate_Result
     */
    protected function _getQuotes()
    {
        $rawRequest = $this->_rawRequest;
        $xmlStr = '<?xml version = "1.0" encoding = "UTF-8"?>'
                . '<p:DCTRequest xmlns:p="http://www.dhl.com" xmlns:p1="http://www.dhl.com/datatypes" '
                . 'xmlns:p2="http://www.dhl.com/DCTRequestdatatypes" '
                . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                . 'xsi:schemaLocation="http://www.dhl.com DCT-req.xsd "/>';
        $xml = new SimpleXMLElement($xmlStr);
        $nodeGetQuote = $xml->addChild('GetQuote', '', '');
        $nodeRequest = $nodeGetQuote->addChild('Request');

        $nodeServiceHeader = $nodeRequest->addChild('ServiceHeader');
        $nodeServiceHeader->addChild('SiteID', (string)$this->getConfigData('id'));
        $nodeServiceHeader->addChild('Password', (string)$this->getConfigData('password'));

        $nodeFrom = $nodeGetQuote->addChild('From');
        $nodeFrom->addChild('CountryCode', $rawRequest->getOrigCountryId());
        $nodeFrom->addChild('Postalcode', $rawRequest->getOrigPostal());
        $nodeFrom->addChild('City', $rawRequest->getOrigCity());

        $nodeBkgDetails = $nodeGetQuote->addChild('BkgDetails');
        $nodeBkgDetails->addChild('PaymentCountryCode', $rawRequest->getOrigCountryId());
        $nodeBkgDetails->addChild('Date', Varien_Date::now(true));
        $nodeBkgDetails->addChild('ReadyTime', 'PT' . (int)(string)$this->getConfigData('ready_time') . 'H00M');

        $nodeBkgDetails->addChild('DimensionUnit', $this->_getDimensionUnit());
        $nodeBkgDetails->addChild('WeightUnit', $this->_getWeightUnit());

        $this->_makePieces($nodeBkgDetails);

        $nodeBkgDetails->addChild('PaymentAccountNumber', (string)$this->getConfigData('account'));

        $nodeTo = $nodeGetQuote->addChild('To');
        $nodeTo->addChild('CountryCode', $rawRequest->getDestCountryId());
        $nodeTo->addChild('Postalcode', $rawRequest->getDestPostal());
        $nodeTo->addChild('City', $rawRequest->getDestCity());

        if ($this->getConfigData('content_type') == self::DHL_CONTENT_TYPE_NON_DOC) {
            // IsDutiable flag and Dutiable node indicates that cargo is not a documentation
            $nodeBkgDetails->addChild('IsDutiable', 'Y');
            $nodeDutiable = $nodeGetQuote->addChild('Dutiable');
            $baseCurrencyCode = Mage::app()->getWebsite($this->_request->getWebsiteId())->getBaseCurrencyCode();
            $nodeDutiable->addChild('DeclaredCurrency', $baseCurrencyCode);
            $nodeDutiable->addChild('DeclaredValue', $rawRequest->getValue());
        }

        $request = $xml->asXML();
        $request = utf8_encode($request);
        $responseBody = $this->_getCachedQuotes($request);
        if ($responseBody === null) {
            $debugData = array('request' => $request);
            try {
                $client = new Varien_Http_Client();
                $client->setUri((string)$this->getConfigData('gateway_url'));
                $client->setConfig(array('maxredirects' => 0, 'timeout' => 30));
                $client->setRawData($request);
                $responseBody = $client->request(Varien_Http_Client::POST)->getBody();
                $debugData['result'] = $responseBody;
                $this->_setCachedQuotes($request, $responseBody);
            } catch (Exception $e) {
                $this->_errors[$e->getCode()] = $e->getMessage();
                $responseBody = '';
            }
            $this->_debug($debugData);
        }

        return $this->_parseResponse($responseBody);
    }

    /**
     * Parse response from DHL web service
     *
     * @param string $response
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _parseResponse($response)
    {
        $htmlTranslationTable = get_html_translation_table(HTML_ENTITIES);
        unset($htmlTranslationTable['<'], $htmlTranslationTable['>'], $htmlTranslationTable['"']);
        $response = str_replace(array_keys($htmlTranslationTable), array_values($htmlTranslationTable), $response);

        if (strlen(trim($response)) > 0) {
            if (strpos(trim($response), '<?xml') === 0) {
                $xml = simplexml_load_string($response);
                if (is_object($xml)) {
                    if ((isset($xml->Response->Status->ActionStatus) && $xml->Response->Status->ActionStatus == 'Error')
                        || (isset($xml->GetQuoteResponse->Note->Condition))
                    ) {
                        if (isset($xml->Response->Status->Condition)) {
                            $nodeCondition = $xml->Response->Status->Condition;
                        } else {
                            $nodeCondition = $xml->GetQuoteResponse->Note->Condition;
                        }

                        $code = isset($nodeCondition->ConditionCode) ? (string)$nodeCondition->ConditionCode : 0;
                        $data = isset($nodeCondition->ConditionData) ? (string)$nodeCondition->ConditionData : '';
                        $this->_errors[$code] = Mage::helper('usa')->__('Error #%s : %s', $code, $data);
                    } else {
                        if (isset($xml->GetQuoteResponse->BkgDetails->QtdShp)) {
                            foreach ($xml->GetQuoteResponse->BkgDetails->QtdShp as $quotedShipment) {
                                $this->_addRate($quotedShipment);
                            }
                        } elseif (isset($xml->AirwayBillNumber)) {
                            $result = new Varien_Object();
                            $result->setTrackingNumber((string)$xml->AirwayBillNumber);
                            $result->setShippingLabelContent(base64_decode((string)$xml->Barcodes->OriginDestnBarcode));
                            return $result;
                        } else {

                        }
                    }
                }
            } else {
                $this->_errors[] = Mage::helper('usa')->__('The response is in wrong format.');
            }
        }

        /* @var $result Mage_Shipping_Model_Rate_Result */
        $result = Mage::getModel('shipping/rate_result');
        if ($this->_rates) {
            foreach ($this->_rates as $rate) {
                $method = $rate['service'];
                $data = $rate['data'];
                /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier(self::CODE);
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $rate->setMethodTitle($data['term']);
                $rate->setCost($data['price_total']);
                $rate->setPrice($data['price_total']);
                $result->append($rate);
            }
        } else if (!empty($this->_errors)) {
           return $this->_showError();
        }
        return $result;
    }

    /**
     * Add rate to DHL rates array
     *
     * @param SimpleXMLElement $shipmentDetails
     * @return Mage_Usa_Model_Shipping_Carrier_Dhl_International
     */
    protected function _addRate(SimpleXMLElement $shipmentDetails)
    {
        if (isset($shipmentDetails->ProductShortName)
            && isset($shipmentDetails->ShippingCharge)
            && isset($shipmentDetails->GlobalProductCode)
            && isset($shipmentDetails->CurrencyCode)
            && array_key_exists((string)$shipmentDetails->GlobalProductCode, $this->getAllowedMethods())
        ) {
            // DHL product code, e.g. '3', 'A', 'Q', etc.
            $dhlProduct             = (string)$shipmentDetails->GlobalProductCode;
            $totalEstimate          = (float)(string)$shipmentDetails->ShippingCharge;
            $currencyCode           = (string)$shipmentDetails->CurrencyCode;
            $baseCurrencyCode       = Mage::app()->getWebsite($this->_request->getWebsiteId())->getBaseCurrencyCode();
            $dhlProductDescription  = $this->getDhlProductTitle($dhlProduct);

            if ($currencyCode != $baseCurrencyCode) {
                /* @var $currency Mage_Directory_Model_Currency */
                $currency = Mage::getModel('directory/currency');
                $rates = $currency->getCurrencyRates($currencyCode, array($baseCurrencyCode));
                if (!empty($rates) && isset($rates[$baseCurrencyCode])) {
                    // Convert to store display currency using store exchange rate
                    $totalEstimate = $totalEstimate * $rates[$baseCurrencyCode];
                } else {
                    $rates = $currency->getCurrencyRates($baseCurrencyCode, array($currencyCode));
                    if (!empty($rates) && isset($rates[$currencyCode])) {
                        $totalEstimate = $totalEstimate/$rates[$currencyCode];
                    }
                    if (!isset($rates[$currencyCode]) || !$totalEstimate) {
                        $totalEstimate = false;
                        $this->_errors[] = Mage::helper('usa')->__("Exchange rate %s (Base Currency) -> %s not found. DHL method %s skipped", $currencyCode, $baseCurrencyCode, $dhlProductDescription);
                    }
                }
            }
            if ($totalEstimate) {
                $data = array('term' => $dhlProductDescription,
                    'price_total' => $this->getMethodPrice($totalEstimate, $dhlProduct));
                if (!empty($this->_rates)) {
                    foreach ($this->_rates as $product) {
                        if ($product['data']['term'] == $data['term']
                            && $product['data']['price_total'] == $data['price_total']
                        ) {
                            return $this;
                        }
                    }
                }
                $this->_rates[] = array('service' => $dhlProduct, 'data' => $data);
            }
        }
        return $this;
    }

    /**
     * Returns dimension unit (cm or inch)
     *
     * @return string
     */
    protected function _getDimensionUnit()
    {
        $countryId = $this->_rawRequest->getOrigCountryId();
        $measureUnit = $this->getCountryParams($countryId)->getMeasureUnit();
        if (empty($measureUnit)) {
            Mage::throwException(Mage::helper('usa')->__("Cannot identify measure unit for %s", $countryId));
        }
        return $measureUnit;
    }

    /**
     * Returns weight unit (kg or pound)
     *
     * @return string
     */
    protected function _getWeightUnit()
    {
        $countryId = $this->_rawRequest->getOrigCountryId();
        $weightUnit = $this->getCountryParams($countryId)->getWeightUnit();
        if (empty($weightUnit)) {
            Mage::throwException(Mage::helper('usa')->__("Cannot identify weight unit for %s", $countryId));
        }
        return $weightUnit;
    }

    /**
     * Get Country Params by Country Code
     *
     * @param string $countryCode
     * @return Varien_Object
     *
     * @see $countryCode ISO 3166 Codes (Countries) A2
     */
    protected function getCountryParams($countryCode)
    {
        if (empty($this->_countryParams)) {
            $dhlConfigPath = Mage::getModuleDir('etc', 'Mage_Usa')  . DS . 'dhl' . DS;
            $countriesXml = file_get_contents($dhlConfigPath . 'international' . DS . 'countries.xml');
            $this->_countryParams = new Varien_Simplexml_Element($countriesXml);
        }
        if (isset($this->_countryParams->$countryCode)) {
            $countryParams = new Varien_Object($this->_countryParams->$countryCode->asArray());
        }
        return isset($countryParams) ? $countryParams : new Varien_Object();
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param Varien_Object $request
     * @return Varien_Object
     */
    protected function _doShipmentRequest(Varien_Object $request)
    {
        $this->_prepareShipmentRequest($request);
        $this->_mapRequestToShipment($request);
        $this->setRequest($request);

        return $this->_doRequest();
    }

    /**
     * Processing additional validation to check is carrier applicable.
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Carrier_Abstract|Mage_Shipping_Model_Rate_Result_Error|boolean
     */
    public function proccessAdditionalValidation(Mage_Shipping_Model_Rate_Request $request)
    {
        //Skip by item validation if there is no items in request
        if(!count($this->getAllItems($request))) {
            $this->_errors[] = Mage::helper('usa')->__('There is no items in this order');
        }

        if (!empty($this->_errors)) {
            return $this->_showError();
        }

        return $this;
    }

    /**
     * Show default error
     *
     * @return bool|Mage_Shipping_Model_Rate_Result_Error
     */
    protected function _showError()
    {
        $showMethod = $this->getConfigData('showmethod');

        if ($showMethod) {
            /* @var $error Mage_Shipping_Model_Rate_Result_Error */
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier(self::CODE);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $this->_debug($this->_errors);
            return $error;
        } else {
            return false;
        }
    }

    /**
     * Return container types of carrier
     *
     * @param Varien_Object|null $params
     * @return array|bool
     */
    public function getContainerTypes(Varien_Object $params = null)
    {
        return array(
            self::DHL_CONTENT_TYPE_DOC      => Mage::helper('usa')->__('Documents'),
            self::DHL_CONTENT_TYPE_NON_DOC  => Mage::helper('usa')->__('Non Documents')
        );
    }

    /**
     * Map request to shipment
     *
     * @param Varien_Object $request
     * @return null
     */
    protected function _mapRequestToShipment(Varien_Object $request)
    {
        $customsValue = $request->getPackageParams()->getCustomsValue();


        $request->getLimitMethod($request->getShippingMethod());
        $request->getPackageValue($customsValue);
        $request->getValueWithDiscount($customsValue);
        $request->getPackageCustomsValue($customsValue);
        $request->getFreeMethodWeight(0);
        $request->getDhlShipmentType($request->getPackagingType());
    }

    /**
     * Do rate request and handle errors
     *
     * @return Mage_Shipping_Model_Rate_Result|Varien_Object
     */
    protected function _doRequest()
    {
        $rawRequest = $this->_request;

        $origCountryPath = 'shipping/origin/country_id';
        $originRegion = (string)$this->getCountryParams(
            Mage::getStoreConfig($origCountryPath, $this->getStore())
        )->region;

        if (!$originRegion) {
            Mage::throwException(Mage::helper('usa')->__('Wrong Region.'));
        }

        if ($originRegion == 'AM') {
            $originRegion = '';
        }

        $xmlStr = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<req:ShipmentValidateRequest' . $originRegion
            . ' xmlns:req="http://www.dhl.com"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
            . ' xsi:schemaLocation="http://www.dhl.com ship-val-req'
            . ($originRegion ? '_' . $originRegion : '') . '.xsd" />';
        $xml = new SimpleXMLElement($xmlStr);

        $nodeRequest = $xml->addChild('Request', '', '');
        $nodeServiceHeader = $nodeRequest->addChild('ServiceHeader');
        $nodeServiceHeader->addChild('SiteID', (string)$this->getConfigData('id'));
        $nodeServiceHeader->addChild('Password', (string)$this->getConfigData('password'));

        $xml->addChild('LanguageCode', 'EN', '');
        $xml->addChild('PiecesEnabled', 'Y', '');

        /* Billing */
        $nodeBilling = $xml->addChild('Billing', '', '');
        $nodeBilling->addChild('ShipperAccountNumber', (string)$this->getConfigData('account'));
        /*
         * Method of Payment:
         * S (Shipper)
         * R (Receiver)
         * T (Third Party)
         */
        $nodeBilling->addChild('ShippingPaymentType', 'S');

        /*
         * Shipment bill to account – required if Shipping PaymentType is other than 'S'
         */
        //$nodeBilling->addChild('BillingAccountNumber', (string)$this->getConfigData('password'));
        //$nodeBilling->addChild('DutyPaymentType', '');
        //$nodeBilling->addChild('DutyAccountNumber', '');

        /* Receiver */
        $nodeConsignee = $xml->addChild('Consignee', '', '');

        $companyName = ($rawRequest->getRecipientContactCompanyName())
                ? $rawRequest->getRecipientContactCompanyName()
                : $rawRequest->getRecipientContactPersonName();

        $nodeConsignee->addChild('CompanyName', substr($companyName, 0, 35));

        $address = $rawRequest->getRecipientAddressStreet1(). ' ' . $rawRequest->getRecipientAddressStreet2();
        if (strlen($address) > 35) {
            for ($i = 0; $i < strlen($address); $i += 35) {
                $nodeConsignee->addChild('AddressLine', substr($address, $i, 35));
            }
        } else {
            $nodeConsignee->addChild('AddressLine', $address);
        }

        $nodeConsignee->addChild('City', $rawRequest->getRecipientAddressCity());
        $nodeConsignee->addChild('Division', $rawRequest->getRecipientAddressStateOrProvinceCode());
        $nodeConsignee->addChild('PostalCode', $rawRequest->getRecipientAddressPostalCode());
        $nodeConsignee->addChild('CountryCode', $rawRequest->getRecipientAddressCountryCode());
        $nodeConsignee->addChild('CountryName',
            (string)$this->getCountryParams($rawRequest->getRecipientAddressCountryCode())->name);
        $nodeContact = $nodeConsignee->addChild('Contact');
        $nodeContact->addChild('PersonName', substr($rawRequest->getRecipientContactPersonName(), 0, 34));
        $nodeContact->addChild('PhoneNumber', substr($rawRequest->getRecipientContactPhoneNumber(), 0, 24));

        /* Commodity */
        /*
         * The CommodityCode element contains commodity code for shipment contents. Its
         * value should lie in between 1 to 9999.This field is mandatory.
         */
        $nodeCommodity = $xml->addChild('Commodity', '', '');
        $nodeCommodity->addChild('CommodityCode', '1');

        /* Dutiable */
        if ($this->getConfigData('content_type') == self::DHL_CONTENT_TYPE_NON_DOC) {
            $nodeDutiable = $xml->addChild('Dutiable', '', '');
            $nodeDutiable->addChild('DeclaredValue',
                round($rawRequest->getOrderShipment()->getOrder()->getSubtotal(),2));
            $baseCurrencyCode = Mage::app()->getWebsite($rawRequest->getWebsiteId())->getBaseCurrencyCode();
            $nodeDutiable->addChild('DeclaredCurrency', $baseCurrencyCode);
        }

        /* Reference */
        /*
         * This element identifies the reference information. It is an optional field in the
         * shipment validation request. Only the first reference will be taken currently.
         */
        $nodeReference = $xml->addChild('Reference', '', '');
        $nodeReference->addChild('ReferenceID', 'shipment reference');
        $nodeReference->addChild('ReferenceType', 'St');

        /* Shipment Details */
        $nodeShipmentDetails = $xml->addChild('ShipmentDetails', '', '');
        $nodeShipmentDetails->addChild('NumberOfPieces', count($rawRequest->getPackages()));
        $nodeShipmentDetails->addChild('CurrencyCode',
            Mage::app()->getWebsite($this->_request->getWebsiteId())->getBaseCurrencyCode());
        $nodePieces = $nodeShipmentDetails->addChild('Pieces', '', '');

        /*
         * Package type
         * EE (DHL Express Envelope), OD (Other DHL Packaging), CP (Custom Packaging)
         * DC (Document), DM (Domestic), ED (Express Document), FR (Freight)
         * BD (Jumbo Document), BP (Jumbo Parcel), JD (Jumbo Junior Document)
         * JP (Jumbo Junior Parcel), PA (Parcel), DF (DHL Flyer)
         */
        foreach ($rawRequest->getPackages() as $package) {
            $nodePiece = $nodePieces->addChild('Piece', '', '');
            $packageType = 'DC';
            if ($package['params']['container'] == self::DHL_CONTENT_TYPE_NON_DOC) {
                $packageType = 'CP';
            }
            //$niodePiece->addChild('PieceID', '');
            $nodePiece->addChild('PackageType', $packageType);
            $nodePiece->addChild('Weight', $package['params']['weight']);
            $nodePiece->addChild('Depth', $package['params']['length']);
            $nodePiece->addChild('Width', $package['params']['width']);
            $nodePiece->addChild('Height', $package['params']['height']);
        }

        if ($package['params']['container'] == self::DHL_CONTENT_TYPE_NON_DOC) {
            $packageType = 'CP';
        }
        $nodeShipmentDetails->addChild('PackageType', $packageType);
        $nodeShipmentDetails->addChild('Weight', $rawRequest->getPackageWeight());

        $dimensionUnit  = $rawRequest->getPackageParams()->getDimensionUnits();
        $weightUnits    = $rawRequest->getPackageParams()->getWeightUnits();

        $nodeShipmentDetails->addChild('DimensionUnit', $dimensionUnit[0]);
        $nodeShipmentDetails->addChild('WeightUnit',  $weightUnits[0]);

        $nodeShipmentDetails->addChild('GlobalProductCode', $rawRequest->getShippingMethod());
        $nodeShipmentDetails->addChild('LocalProductCode', $rawRequest->getShippingMethod());

        /*
         * The DoorTo Element defines the type of delivery service that applies to the shipment.
         * The valid values are DD (Door to Door), DA (Door to Airport) , AA and DC (Door to
         * Door non-compliant)
         */
        $nodeShipmentDetails->addChild('DoorTo', 'DD');
        $nodeShipmentDetails->addChild('Date', Mage::getModel('core/date')->date('Y-m-d'));
        $nodeShipmentDetails->addChild('Contents', 'DHL Parcel TEST');
        if ($this->getConfigData('content_type') == self::DHL_CONTENT_TYPE_NON_DOC) {
            $nodeShipmentDetails->addChild('IsDutiable', 'Y');
        }

        /* Shipper */
        $nodeShipper = $xml->addChild('Shipper', '', '');
        $nodeShipper->addChild('ShipperID', (string)$this->getConfigData('account'));
        $nodeShipper->addChild('CompanyName', $rawRequest->getShipperContactCompanyName());
        //$nodeShipper->addChild('RegisteredAccount', '');

        $address = $rawRequest->getShipperAddressStreet1(). ' ' . $rawRequest->getShipperAddressStreet2();
        if (strlen($address) > 35) {
            for ($i = 0; $i < strlen($address); $i += 35) {
                $nodeShipper->addChild('AddressLine', substr($address, $i, 35));
            }
        } else {
            $nodeShipper->addChild('AddressLine', $address);
        }

        $nodeShipper->addChild('City', $rawRequest->getShipperAddressCity());
        $nodeShipper->addChild('Division', $rawRequest->getShipperAddressStateOrProvinceCode());
        $nodeShipper->addChild('PostalCode', $rawRequest->getShipperAddressPostalCode());
        $nodeShipper->addChild('CountryCode', $rawRequest->getShipperAddressCountryCode());
        $nodeShipper->addChild('CountryName',
            (string)$this->getCountryParams($rawRequest->getShipperAddressCountryCode())->name);
        $nodeContact = $nodeShipper->addChild('Contact', '', '');
        $nodeContact->addChild('PersonName', substr($rawRequest->getShipperContactPersonName(), 0, 34));
        $nodeContact->addChild('PhoneNumber', substr($rawRequest->getShipperContactPhoneNumber(), 0, 24));

        $request = $xml->asXML();
        $request = utf8_encode($request);
        $responseBody = $this->_getCachedQuotes($request);
        if ($responseBody === null) {
            $debugData = array('request' => $request);
            try {
                $client = new Varien_Http_Client();
                $client->setUri((string)$this->getConfigData('gateway_url'));
                $client->setConfig(array('maxredirects' => 0, 'timeout' => 30));
                $client->setRawData($request);
                $responseBody = $client->request(Varien_Http_Client::POST)->getBody();
                $debugData['result'] = $responseBody;
                $this->_setCachedQuotes($request, $responseBody);
            } catch (Exception $e) {
                $this->_errors[$e->getCode()] = $e->getMessage();
                $responseBody = '';
            }
            $this->_debug($debugData);
        }

        return $this->_parseResponse($responseBody);
    }

    /**
     * Get tracking
     *
     * @param mixed $trackings
     * @return mixed
     */
    public function getTracking($trackings)
    {
        if (!is_array($trackings)) {
            $trackings = array($trackings);
        }
        $this->_getXMLTracking($trackings);

        return $this->_result;
    }

    /**
     * Send request for tracking
     *
     * @param array $trackings
     * @return void
     */
    protected function _getXMLTracking($trackings)
    {
        $xmlStr = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<req:KnownTrackingRequest'
            . ' xmlns:req="http://www.dhl.com"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
            . ' xsi:schemaLocation="http://www.dhl.com TrackingRequestKnown.xsd" />';

        $xml = new SimpleXMLElement($xmlStr);

        $requestNode = $xml->addChild('Request', '', '');
        $serviceHeaderNode = $requestNode->addChild('ServiceHeader', '', '');
        $serviceHeaderNode->addChild('SiteID', (string)$this->getConfigData('id'));
        $serviceHeaderNode->addChild('Password', (string)$this->getConfigData('password'));

        $xml->addChild('LanguageCode', 'EN', '');
        foreach ($trackings as $tracking) {
            $xml->addChild('AWBNumber', $tracking, '');
        }
        /*
         * Checkpoint details selection flag
         * LAST_CHECK_POINT_ONLY
         * ALL_CHECK_POINTS
         */
        $xml->addChild('LevelOfDetails', 'ALL_CHECK_POINTS', '');

        /*
         * Value that indicates for getting the tracking details with the additional
         * piece details and its respective Piece Details, Piece checkpoints along with
         * Shipment Details if queried.
         *
         * S-Only Shipment Details
         * B-Both Shipment & Piece Details
         * P-Only Piece Details
         * Default is ‘S’
         */
        //$xml->addChild('PiecesEnabled', 'ALL_CHECK_POINTS');

        $request = $xml->asXML();
        $request = utf8_encode($request);

        $responseBody = $this->_getCachedQuotes($request);
        if ($responseBody === null) {
            $debugData = array('request' => $request);
            try {
                $client = new Varien_Http_Client();
                $client->setUri((string)$this->getConfigData('gateway_url'));
                $client->setConfig(array('maxredirects' => 0, 'timeout' => 30));
                $client->setRawData($request);
                $responseBody = $client->request(Varien_Http_Client::POST)->getBody();
                $debugData['result'] = $responseBody;
                $this->_setCachedQuotes($request, $responseBody);
            } catch (Exception $e) {
                $this->_errors[$e->getCode()] = $e->getMessage();
                $responseBody = '';
            }
            $this->_debug($debugData);
        }


        $this->_parseXmlTrackingResponse($trackings, $responseBody);
    }

    /**
     * Parse xml tracking response
     *
     * @param array $trackings
     * @param string $response
     * @return void
     */
    protected function _parseXmlTrackingResponse($trackings, $response)
    {
        $errorTitle = Mage::helper('usa')->__('Unable to retrieve tracking');
        $resultArr = array();
        $errorArr = array();
        $trackNum = '';

        $htmlTranslationTable = get_html_translation_table(HTML_ENTITIES);
        unset($htmlTranslationTable['<'], $htmlTranslationTable['>'], $htmlTranslationTable['"']);
        $response = str_replace(array_keys($htmlTranslationTable), array_values($htmlTranslationTable), $response);

        if (strlen(trim($response)) > 0) {
            if (strpos(trim($response), '<?xml') === 0) {
                $xml = simplexml_load_string($response);
                if (is_object($xml)) {
                    if ((isset($xml->Response->Status->ActionStatus)
                        && $xml->Response->Status->ActionStatus == 'Failure')
                        || (isset($xml->GetQuoteResponse->Note->Condition))
                    ) {
                        if (isset($xml->Response->Status->Condition)) {
                            $nodeCondition = $xml->Response->Status->Condition;
                        }

                        $code = isset($nodeCondition->ConditionCode) ? (string)$nodeCondition->ConditionCode : 0;
                        $data = isset($nodeCondition->ConditionData) ? (string)$nodeCondition->ConditionData : '';
                        $this->_errors[$code] = Mage::helper('usa')->__('Error #%s : %s', $code, $data);
                    } elseif (is_object($xml) && is_object($xml->AWBInfo)) {
                        foreach ($xml->AWBInfo as $awbinfo) {
                            $awbinfoData = array();
                            $trackNum = isset($awbinfo->AWBNumber) ? (string)$awbinfo->AWBNumber : '';
                            if (is_object($awbinfo) && $awbinfo->ShipmentInfo) {
                                $shipmentInfo = $awbinfo->ShipmentInfo;

                                if ($shipmentInfo->ShipmentDesc) {
                                    $awbinfoData['service'] = (string)$shipmentInfo->ShipmentDesc;
                                }

                                $awbinfoData['weight'] = (string)$shipmentInfo->Weight
                                    . ' ' . (string)$shipmentInfo->WeightUnit;

                                $packageProgress = array();
                                if (isset($shipmentInfo->ShipmentEvent)) {
                                    foreach ($shipmentInfo->ShipmentEvent as $shipmentEvent) {
                                        $shipmentEventArray = array();
                                        $shipmentEventArray['activity']         =
                                            (string)$shipmentEvent->ServiceEvent->EventCode
                                            . ' '
                                            . (string)$shipmentEvent->ServiceEvent->Description;
                                        $shipmentEventArray['deliverydate']     = (string)$shipmentEvent->Date;
                                        $shipmentEventArray['deliverytime']     = (string)$shipmentEvent->Time;

                                        $shipmentEventArray['deliverylocation'] =
                                            (string)$shipmentEvent->ServiceArea->Description
                                            . ' '
                                            . '[' . (string)$shipmentEvent->ServiceArea->ServiceAreaCode . ']';
                                        $packageProgress[] = $shipmentEventArray;
                                    }
                                    $awbinfoData['progressdetail'] = $packageProgress;
                                }
                                $resultArr[$trackNum] = $awbinfoData;
                            } else {
                                $errorArr[$trackNum] = Mage::helper('usa')->__('Unable to retrieve tracking');
                            }
                        }
                    }
                }
            } else {
                $errorTitle = Mage::helper('usa')->__('Response is in the wrong format');
            }
        }

        $result = Mage::getModel('shipping/tracking_result');
        if ($errorArr || $resultArr) {
            foreach ($errorArr as $trackNum => $err) {
                $error = Mage::getModel('shipping/tracking_result_error');
                $error->setCarrier($this->_code);
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setTracking($trackNum);
                $error->setErrorMessage($err);
                $result->append($error);
            }

            foreach ($resultArr as $trackNum => $data) {
                $tracking = Mage::getModel('shipping/tracking_result_status');
                $tracking->setCarrier($this->_code);
                $tracking->setCarrierTitle($this->getConfigData('title'));
                $tracking->setTracking($trackNum);
                $tracking->addData($data);

                $result->append($tracking);
            }
        } else {
            foreach ($trackings as $trackNum) {
                $error = Mage::getModel('shipping/tracking_result_error');
                $error->setCarrier($this->_code);
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setTracking($trackNum);
                $error->setErrorMessage($errorTitle);
                $result->append($error);
            }
        }
        $this->_result = $result;
    }
}
