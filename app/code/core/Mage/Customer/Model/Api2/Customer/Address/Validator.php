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
     * Separator for multistreet
     */
    const STREET_SEPARATOR = '; ';

    /**
     * Filter request data.
     *
     * @param  array $data
     * @return array Filtered data
     */
    public function filter(array $data)
    {
        $data = parent::filter($data);

        // If the array contains more than two elements, then combine the extra elements in a string
        if (isset($data['street']) && is_array($data['street']) && count($data['street']) > 2) {
            $data['street'][1] .= self::STREET_SEPARATOR
                . implode(self::STREET_SEPARATOR, array_slice($data['street'], 2));
            $filteredData['street'] = array_slice($data['street'], 0, 2);
        }

        return $data;
    }

    /**
     * Validate data for create association with the country
     *
     * @param array $data
     * @return bool
     */
    public function isValidDataForCreateAssociationWithCountry(array $data)
    {
        $isValid = true;
        if (!array_key_exists('country_id', $data) || '' == trim($data['country_id'])) {
            $this->_addError('"Country" is required.');
            $isValid = false;
        } else {
            if (!is_string($data['country_id'])) {
                $this->_addError('Invalid country identifier type.');
                $isValid = false;
            } else {
                $validator = new Zend_Validate_Between(array('min' => 2, 'max' => 3, 'inclusive' => true));
                if (!$validator->isValid($data['country_id'])) {
                    $this->_addError("Country is not between '2' and '3', inclusively.");
                    $isValid = false;
                } else {
                    /* @var $country Mage_Directory_Model_Country */
                    $country = Mage::getModel('directory/country')->loadByCode($data['country_id']);
                    if (!$country->getId()) {
                        $this->_addError('Country does not exist.');
                        $isValid = false;
                    }
                }
            }
        }

        // break the validation if the country is not valid
        if (!$isValid) {
            return false;
        }

        /* @var $regions Mage_Directory_Model_Resource_Region_Collection */
        $regions = $country->getRegions();

        // Is it the country with predifined regions?
        if ($regions->count()) {
            if (!array_key_exists('region', $data)) {
                $this->_addError('State/Province is required');
                $isValid = false;
            } else {
                $count = $regions->addFieldToFilter(array('default_name', 'code'),
                    array($data['region'], $data['region']))
                    ->clear()
                    ->count();
                if (!$count) {
                    $this->_addError('Region does not exist');
                    $isValid = false;
                }
            }
        }

        return $isValid;
    }

    /**
     * Validate data for change association with the country
     *
     * @param Mage_Customer_Model_Address $address
     * @param array $data
     * @return bool
     */
    public function isValidDataForChangeAssociationWithCountry(Mage_Customer_Model_Address $address, array $data)
    {
        $isValid = true;

        if (!isset($data['country_id']) && !isset($data['region'])) {
            return $isValid;
        }

        // Check the country
        if (isset($data['country_id'])) {
            if (!is_string($data['country_id'])) {
                $this->_addError('Invalid country identifier type');
                $isValid = false;
            } else {
                /* @var $country Mage_Directory_Model_Country */
                $country = Mage::getModel('directory/country')->loadByCode($data['country_id']);
                if (!$country->getId()) {
                    $this->_addError('Country does not exist');
                    $isValid = false;
                }
            }
            // break the validation if the country is not valid
            if (!$isValid) {
                return false;
            }
        }

        // if the country is not passed load the current country
        if (!isset($country)) {
            /* @var $country Mage_Directory_Model_Country */
            $country = $address->getCountryModel();
        }
        /* @var $regions Mage_Directory_Model_Resource_Region_Collection */
        $regions = $country->getRegions();

        // Check the region
        if (!isset($data['region'])) {
            // Is it the country with predifined regions?
            if ($regions->count()) {
                $this->_addError('State/Province is required');
            }
        } else {
            if (!is_string($data['region']) || ($regions->count() && empty($data['region']))) {
                $this->_addError('Invalid State/Province type');
                $isValid = false;
            } else {
                // Is it the country with predifined regions?
                if ($regions->count()) {
                    $count = $regions->addFieldToFilter(array('default_name', 'code'),
                        array($data['region'], $data['region']))
                        ->clear()
                        ->count();
                    if (!$count) {
                        $this->_addError('Region does not exist');
                        $isValid = false;
                    }
                }
            }
        }

        return $isValid;
    }

    /**
     * Returns an array of errors
     *
     * @return array
     */
    public function getErrors2()
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
}
