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
 * @package     Mage_Directory
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Directory Country Resource Collection
 *
 * @category    Mage
 * @package     Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Resource_Country_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('directory/country');
    }

    /**
     * Load allowed countries for current store
     *
     * @return Mage_Directory_Model_Resource_Country_Collection
     */
    public function loadByStore()
    {
        $allowCountries = explode(',', (string)Mage::getStoreConfig('general/country/allow'));
        if (!empty($allowCountries)) {
            $this->addFieldToFilter("country_id", array('in'=>$allowCountries));
        }
        return $this;
    }

    /**
     * Loads Item By Id
     *
     * @param string $countryId
     * @return Mage_Directory_Model_Resource_Country
     */
    public function getItemById($countryId)
    {
        foreach ($this->_items as $country) {
            if ($country->getCountryId() == $countryId) {
                return $country;
            }
        }
        return Mage::getResourceModel('directory/country');
    }

    /**
     * Add filter by country code to collection
     *
     * @param string|array $countryCode
     * @param string|array $iso
     * @return Mage_Directory_Model_Resource_Country_Collection
     */
    public function addCountryCodeFilter($countryCode, $iso = array('iso3', 'iso2'))
    {
        if (!empty($countryCode)) {
            if (is_array($countryCode)) {
                if (is_array($iso)) {
                    $i = 0;
                    foreach ($iso as $iso_curr) {
                        $this->addFieldToFilter("{$iso_curr}_code", array('in'=>$countryCode));
                    }
                } else {
                    $this->addFieldToFilter("{$iso}_code", array('in'=>$countryCode));
                }
            } else {
                if (is_array($iso)) {
                    $i = 0;
                    foreach ($iso as $iso_curr) {
                        $this->addFieldToFilter("{$iso_curr}_code", $countryCode);
                    }
                } else {
                    $this->addFieldToFilter("{$iso}_code", $countryCode);
                }
            }
        }
        return $this;
    }

    /**
     * Add filter by country code(s) to collection
     *
     * @param string|array $countryId
     * @return Mage_Directory_Model_Resource_Country_Collection
     */
    public function addCountryIdFilter($countryId)
    {
        if (!empty($countryId)) {
            if (is_array($countryId)) {
                $this->addFieldToFilter("country_id", array('in'=>$countryId));
            } else {
                $this->addFieldToFilter("country_id", $countryId);
            }
        }
        return $this;
    }

    /**
     * Convert collection items to select options array
     *
     * @param string $emptyLabel
     * @return array
     */
    public function toOptionArray($emptyLabel = ' ')
    {
        $options = $this->_toOptionArray('country_id', 'name', array('title'=>'iso2_code'));

        $sort = array();
        foreach ($options as $index=>$data) {
            $name = Mage::app()->getLocale()->getCountryTranslation($data['value']);
            if (!empty($name)) {
                $sort[$name] = $data['value'];
            }
        }

        ksort($sort);
        $options = array();
        foreach ($sort as $label=>$value) {
            $options[] = array(
               'value' => $value,
               'label' => $label
            );
        }

        if (count($options)>0 && $emptyLabel !== false) {
            array_unshift($options, array('value'=>'', 'label'=>$emptyLabel));
        }
        return $options;
    }
}
