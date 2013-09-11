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
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Model\Address;

class AbstractAddress extends \Magento\Core\Model\AbstractModel
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
     * \Directory country models
     *
     * @var array
     */
    static protected $_countryModels = array();

    /**
     * \Directory region models
     *
     * @var array
     */
    static protected $_regionModels = array();

    /**
     * Enforce format of the street field
     *
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $data = $this->_implodeStreetField($data);
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Get full customer name
     *
     * @return string
     */
    public function getName()
    {
        $name = '';
        $config = \Mage::getSingleton('Magento\Eav\Model\Config');
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
     * @return \Magento\Customer\Model\Address\AbstractAddress
     */
    public function setStreetFull($street)
    {
        return $this->setStreet($street);
    }

    /**
     * Non-magic setter for a street field
     *
     * @param string|array $street
     * @return \Magento\Customer\Model\Address\AbstractAddress
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
     * @return \Magento\Object
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
                $this->unsRegion();
            } else {
                $regionModel = \Mage::getModel('Magento\Directory\Model\Region')
                    ->loadByCode($this->getRegionCode(), $this->getCountryId());
                $this->setData('region_id', $regionModel->getId());
            }
        }
        return $this->getData('region_id');
    }

    public function getCountry()
    {
        /*if ($this->getData('country_id') && !$this->getData('country')) {
            $this->setData('country', \Mage::getModel('Magento\Directory\Model\Country')
                ->load($this->getData('country_id'))->getIso2Code());
        }
        return $this->getData('country');*/
        $country = $this->getCountryId();
        return $country ? $country : $this->getData('country');
    }

    /**
     * Retrive country model
     *
     * @return \Magento\Directory\Model\Country
     */
    public function getCountryModel()
    {
        if(!isset(self::$_countryModels[$this->getCountryId()])) {
            self::$_countryModels[$this->getCountryId()] = \Mage::getModel('Magento\Directory\Model\Country')
                ->load($this->getCountryId());
        }

        return self::$_countryModels[$this->getCountryId()];
    }

    /**
     * Retrive country model
     *
     * @return \Magento\Directory\Model\Country
     */
    public function getRegionModel($region=null)
    {
        if(is_null($region)) {
            $region = $this->getRegionId();
        }

        if(!isset(self::$_regionModels[$region])) {
            self::$_regionModels[$region] = \Mage::getModel('Magento\Directory\Model\Region')->load($region);
        }

        return self::$_regionModels[$region];
    }

    public function format($type)
    {
        if(!($formatType = $this->getConfig()->getFormatByCode($type))
            || !$formatType->getRenderer()) {
            return null;
        }
        \Mage::dispatchEvent('customer_address_format', array('type' => $formatType, 'address' => $this));
        return $formatType->getRenderer()->render($this);
    }

    /**
     * Retrive address config object
     *
     * @return \Magento\Customer\Model\Address\Config
     */
    public function getConfig()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Address\Config');
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

        $_havingOptionalZip = \Mage::helper('Magento\Directory\Helper\Data')->getCountriesWithOptionalZip();
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
               && \Mage::helper('Magento\Directory\Helper\Data')->isRegionRequired($this->getCountryId())
        ) {
            $errors[] = __('Please enter the state/province.');
        }

        if (empty($errors) || $this->getShouldIgnoreValidation()) {
            return true;
        }
        return $errors;
    }
}
