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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 class for customer address rest
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Api2_Customer_Address_Validator extends Mage_Api2_Model_Resource_Validator_Eav
{
    /**
     * A list of all available countries in a form of country ID => array(region name => region ID, ...)
     *
     * @var array
     */
    protected $_countries = null;

    /**
     * Validatior object constructor
     *
     * @param array $options
     */
    public function __construct($options)
    {
        parent::__construct($options);

        $this->_loadCountries();
    }

    /**
     * Lazy load and return country list
     *
     * @return array|null
     */
    protected function _loadCountries()
    {
        if (null === $this->_countries) {
            /** @var $countriesCollection Mage_Directory_Model_Resource_Country_Collection */
            $countriesCollection = Mage::getResourceModel('directory/country_collection');
            $this->_countries    = array();

            foreach ($countriesCollection as $country) {
                $this->_countries[$country->getId()] = array();
            }
            /** @var $regionsCollection Mage_Directory_Model_Resource_Region_Collection */
            $regionsCollection = Mage::getResourceModel('directory/region_collection');

            foreach ($regionsCollection->getItems() as $region) {
                $this->_countries[$region->getCountryId()][$region->getName()] = $region->getId();
                $this->_countries[$region->getCountryId()][$region->getCode()] = $region->getId();
            }
        }
        return $this->_countries;
    }

    /**
     * Validate country identifier
     *
     * @param string $countryId
     * @return bool
     */
    protected function _isCountryIdValid($countryId)
    {
        if (!is_string($countryId)) {
            $this->_addError('Invalid country identifier type');

            return false;
        }
        if (!isset($this->_countries[$countryId])) {
            $this->_addError('Country does not exist');

            return false;
        }
        return true;
    }

    /**
     * Validates region
     *
     * @param string $countryId
     * @param string $region
     * @return bool
     */
    protected function _isCountryRegionValid($countryId, $region)
    {
        if (!is_string($region) && false !== $region) {
            $this->_addError('Invalid State/Province type');

            return false;
        }
        $region = trim($region);

        if (!empty($this->_countries[$countryId])) {
            if (!strlen($region)) {
                $this->_addError('State/Province is required');

                return false;
            }
            if (!isset($this->_countries[$countryId][$region])) {
                $this->_addError('State/Province is invalid');

                return false;
            }
        }
        return true;
    }

    /**
     * Filter request data.
     *
     * @param  array $data
     * @return array Filtered data
     */
    public function filter(array $data)
    {
        foreach ($data as &$field) {
            if (is_string($field) && !strlen(trim($field))) {
                $field = null;
            }
        }
        unset($field);

        return parent::filter($data);
    }

    /**
     * Return country regions
     *
     * @param string $countryId Country identifier
     * @return array
     */
    public function getCountryRegions($countryId)
    {
        return isset($this->_countries[$countryId]) ? $this->_countries[$countryId] : array();
    }

    /**
     * Returns an array of errors
     *
     * @return array
     */
    public function getErrors()
    {
        // business asked to avoid additional validation message, so we filter it here
        $errors        = array();
        $helper        = Mage::helper('eav');
        $requiredAttrs = array();
        $isRequiredRE  = '/^' . str_replace('%s', '(.+)', preg_quote($helper->__('"%s" is a required value.'))). '$/';
        $greaterThanRE = '/^' . str_replace(
            '%s', '(.+)', preg_quote($helper->__('"%s" length must be equal or greater than %s characters.'))
        ) . '$/';

        // find all required attributes labels
        foreach ($this->_errors as $error) {
            if (preg_match($isRequiredRE, $error, $matches)) {
                $requiredAttrs[$matches[1]] = true;
            }
        }
        // exclude additional messages for required attributes been failed
        foreach ($this->_errors as $error) {
            if (preg_match($isRequiredRE, $error)
                || !preg_match($greaterThanRE, $error, $matches)
                || !isset($requiredAttrs[$matches[1]])) {
                $errors[] = $error;
            }
        }
        return $errors;
    }

    /**
     * Validate entity
     *
     * @param array $data
     * @return bool
     */
    public function isValidData(array $data)
    {
        return parent::isValidData($data)
            && $this->_isCountryIdValid($data['country_id'])
            && $this->_isCountryRegionValid($data['country_id'], $data['region']);
    }
}
