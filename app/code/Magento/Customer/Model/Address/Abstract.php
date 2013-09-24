<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Address abstract model
 *
 * @method string getPrefix()
 * @method string getSuffix()
 * @method string getFirstname()
 * @method string getMiddlename()
 * @method string getLastname()
 * @method int getCountryId()
 */
class Magento_Customer_Model_Address_Abstract extends Magento_Core_Model_Abstract
{
    /**
     * Possible customer address types
     */
    const TYPE_BILLING  = 'billing';
    const TYPE_SHIPPING = 'shipping';

    /**
     * Prefix of model events
     *
     * @var string
     */
    protected $_eventPrefix = 'customer_address';

    /**
     * Name of event object
     *
     * @var string
     */
    protected $_eventObject = 'customer_address';

    /**
     * Directory country models
     *
     * @var Magento_Directory_Model_Country[]
     */
    static protected $_countryModels = array();

    /**
     * Directory region models
     *
     * @var Magento_Directory_Model_Region[]
     */
    static protected $_regionModels = array();

    /**
     * Directory data
     *
     * @var Magento_Directory_Helper_Data
     */
    protected $_directoryData = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * @var Magento_Customer_Model_Address_Config
     */
    protected $_addressConfig;

    /**
     * @var Magento_Directory_Model_RegionFactory
     */
    protected $_regionFactory;

    /**
     * @var Magento_Directory_Model_CountryFactory
     */
    protected $_countryFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Directory_Helper_Data $directoryData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Customer_Model_Address_Config $addressConfig
     * @param Magento_Directory_Model_RegionFactory $regionFactory
     * @param Magento_Directory_Model_CountryFactory $countryFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Directory_Helper_Data $directoryData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Customer_Model_Address_Config $addressConfig,
        Magento_Directory_Model_RegionFactory $regionFactory,
        Magento_Directory_Model_CountryFactory $countryFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_directoryData = $directoryData;
        $data = $this->_implodeStreetField($data);
        $this->_eavConfig = $eavConfig;
        $this->_addressConfig = $addressConfig;
        $this->_regionFactory = $regionFactory;
        $this->_countryFactory = $countryFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get full customer name
     *
     * @return string
     */
    public function getName()
    {
        $name = '';
        $config = $this->_eavConfig;
        if ($config->getAttribute('customer_address', 'prefix')->getIsVisible() && $this->getPrefix()) {
            $name .= $this->getPrefix() . ' ';
        }
        $name .= $this->getFirstname();
        if ($config->getAttribute('customer_address', 'middlename')->getIsVisible() && $this->getMiddlename()) {
            $name .= ' ' . $this->getMiddlename();
        }
        $name .=  ' ' . $this->getLastname();
        if ($config->getAttribute('customer_address', 'suffix')->getIsVisible() && $this->getSuffix()) {
            $name .= ' ' . $this->getSuffix();
        }
        return $name;
    }

    /**
     * Retrieve street field of an address
     *
     * @param int|null $line Number of a line, value of which to return. Supported values:
     *                       0|null - return array of all lines
     *                       1..n   - return text of individual line
     * @return array|string
     */
    public function getStreet($line = 0)
    {
        $lines = explode("\n", $this->getStreetFull());
        if (0 === $line || $line === null) {
            return $lines;
        } else if (isset($lines[$line - 1])) {
            return $lines[$line - 1];
        } else {
            return '';
        }
    }

    public function getStreet1()
    {
        return $this->getStreet(1);
    }

    public function getStreet2()
    {
        return $this->getStreet(2);
    }

    public function getStreet3()
    {
        return $this->getStreet(3);
    }

    public function getStreet4()
    {
        return $this->getStreet(4);
    }

    /**
     * Retrieve text of street lines, concatenated using LF symbol
     *
     * @return string
     */
    public function getStreetFull()
    {
        return $this->getData('street');
    }

    /**
     * Alias for a street setter. To be used though setDataUsingMethod('street_full', $value).
     *
     * @param string|array $street
     * @return Magento_Customer_Model_Address_Abstract
     */
    public function setStreetFull($street)
    {
        return $this->setStreet($street);
    }

    /**
     * Non-magic setter for a street field
     *
     * @param string|array $street
     * @return Magento_Customer_Model_Address_Abstract
     */
    public function setStreet($street)
    {
        $this->setData('street', $street);
        return $this;
    }

    /**
     * Enforce format of the street field
     *
     * @param array|string $key
     * @param null $value
     * @return Magento_Object
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $key = $this->_implodeStreetField($key);
        } else if ($key == 'street') {
            $value = $this->_implodeStreetValue($value);
        }
        return parent::setData($key, $value);
    }

    /**
     * Implode value of the street field, if it is present among other fields
     *
     * @param array $data
     * @return array
     */
    protected function _implodeStreetField(array $data)
    {
        if (array_key_exists('street', $data)) {
            $data['street'] = $this->_implodeStreetValue($data['street']);
        }
        return $data;
    }

    /**
     * Combine values of street lines into a single string
     *
     * @param array|string $value
     * @return string
     */
    protected function _implodeStreetValue($value)
    {
        if (is_array($value)) {
            $value = trim(implode("\n", $value));
        }
        return $value;
    }

    /**
     * Create fields street1, street2, etc.
     *
     * To be used in controllers for views data
     *
     */
    public function explodeStreetAddress()
    {
        $streetLines = $this->getStreet();
        foreach ($streetLines as $i=>$line) {
            $this->setData('street'.($i+1), $line);
        }
        return $this;
    }

    /**
     * Retrieve region name
     *
     * @return string
     */
    public function getRegion()
    {
        $regionId = $this->getData('region_id');
        $region   = $this->getData('region');

        if ($regionId) {
               if ($this->getRegionModel($regionId)->getCountryId() == $this->getCountryId()) {
                   $region = $this->getRegionModel($regionId)->getName();
                $this->setData('region', $region);
            }
        }

        if (!empty($region) && is_string($region)) {
            $this->setData('region', $region);
        }
        elseif (!$regionId && is_numeric($region)) {
            if ($this->getRegionModel($region)->getCountryId() == $this->getCountryId()) {
                $this->setData('region', $this->getRegionModel($region)->getName());
                $this->setData('region_id', $region);
            }
        }
        elseif ($regionId && !$region) {
               if ($this->getRegionModel($regionId)->getCountryId() == $this->getCountryId()) {
                $this->setData('region', $this->getRegionModel($regionId)->getName());
            }
        }

        return $this->getData('region');
    }

    /**
     * Return 2 letter state code if available, otherwise full region name
     *
     */
    public function getRegionCode()
    {
        $regionId = $this->getData('region_id');
        $region   = $this->getData('region');

        if (!$regionId && is_numeric($region)) {
            if ($this->getRegionModel($region)->getCountryId() == $this->getCountryId()) {
                $this->setData('region_code', $this->getRegionModel($region)->getCode());
            }
        }
        elseif ($regionId) {
            if ($this->getRegionModel($regionId)->getCountryId() == $this->getCountryId()) {
                $this->setData('region_code', $this->getRegionModel($regionId)->getCode());
            }
        }
        elseif (is_string($region)) {
            $this->setData('region_code', $region);
        }
        return $this->getData('region_code');
    }

    public function getRegionId()
    {
        $regionId = $this->getData('region_id');
        $region   = $this->getData('region');
        if (!$regionId) {
            if (is_numeric($region)) {
                $this->setData('region_id', $region);
                //@TODO method unsRegion() is neither defined in abstract model nor in it's children
                $this->unsRegion();
            } else {
                $regionModel = $this->_createRegionInstance()
                    ->loadByCode($this->getRegionCode(), $this->getCountryId());
                $this->setData('region_id', $regionModel->getId());
            }
        }
        return $this->getData('region_id');
    }

    public function getCountry()
    {
        $country = $this->getCountryId();
        return $country ? $country : $this->getData('country');
    }

    /**
     * Retrieve country model
     *
     * @return Magento_Directory_Model_Country
     */
    public function getCountryModel()
    {
        if(!isset(self::$_countryModels[$this->getCountryId()])) {
            $country = $this->_createCountryInstance();
            $country->load($this->getCountryId());
            self::$_countryModels[$this->getCountryId()] = $country;
        }

        return self::$_countryModels[$this->getCountryId()];
    }

    /**
     * Retrieve country model
     *
     * @param int|null $regionId
     * @return Magento_Directory_Model_Region
     */
    public function getRegionModel($regionId = null)
    {
        if(is_null($regionId)) {
            $regionId = $this->getRegionId();
        }

        if(!isset(self::$_regionModels[$regionId])) {
            $region = $this->_createRegionInstance();
            $region->load($regionId);
            self::$_regionModels[$regionId] = $region;
        }

        return self::$_regionModels[$regionId];
    }

    public function format($type)
    {
        if(!($formatType = $this->getConfig()->getFormatByCode($type))
            || !$formatType->getRenderer()) {
            return null;
        }
        $this->_eventManager->dispatch('customer_address_format', array('type' => $formatType, 'address' => $this));
        return $formatType->getRenderer()->render($this);
    }

    /**
     * Retrieve address config object
     *
     * @return Magento_Customer_Model_Address_Config
     */
    public function getConfig()
    {
        return $this->_addressConfig;
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->getRegion();
        return $this;
    }

    /**
     * Validate address attribute values
     *
     * @return bool
     */
    public function validate()
    {
        $errors = array();
        if (!Zend_Validate::is($this->getFirstname(), 'NotEmpty')) {
            $errors[] = __('Please enter the first name.');
        }

        if (!Zend_Validate::is($this->getLastname(), 'NotEmpty')) {
            $errors[] = __('Please enter the last name.');
        }

        if (!Zend_Validate::is($this->getStreet(1), 'NotEmpty')) {
            $errors[] = __('Please enter the street.');
        }

        if (!Zend_Validate::is($this->getCity(), 'NotEmpty')) {
            $errors[] = __('Please enter the city.');
        }

        if (!Zend_Validate::is($this->getTelephone(), 'NotEmpty')) {
            $errors[] = __('Please enter the telephone number.');
        }

        $_havingOptionalZip = $this->_directoryData->getCountriesWithOptionalZip();
        if (!in_array($this->getCountryId(), $_havingOptionalZip)
            && !Zend_Validate::is($this->getPostcode(), 'NotEmpty')
        ) {
            $errors[] = __('Please enter the zip/postal code.');
        }

        if (!Zend_Validate::is($this->getCountryId(), 'NotEmpty')) {
            $errors[] = __('Please enter the country.');
        }

        if ($this->getCountryModel()->getRegionCollection()->getSize()
               && !Zend_Validate::is($this->getRegionId(), 'NotEmpty')
               && $this->_directoryData->isRegionRequired($this->getCountryId())
        ) {
            $errors[] = __('Please enter the state/province.');
        }

        if (empty($errors) || $this->getShouldIgnoreValidation()) {
            return true;
        }
        return $errors;
    }

    /**
     * @return Magento_Directory_Model_Region
     */
    protected function _createRegionInstance()
    {
        return $this->_regionFactory->create();
    }

    /**
     * @return Magento_Directory_Model_Country
     */
    protected function _createCountryInstance()
    {
        return $this->_countryFactory->create();
    }
}
