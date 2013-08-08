<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Directory country format resource model
 *
 * @category    Mage
 * @package     Mage_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Resource_Country_Format_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Directory_Model_Country_Format', 'Mage_Directory_Model_Resource_Country_Format');
    }

    /**
     * Set country filter
     *
     * @param string|Mage_Directory_Model_Country $country
     * @return Mage_Directory_Model_Resource_Country_Format_Collection
     */
    public function setCountryFilter($country)
    {
        if ($country instanceof Mage_Directory_Model_Country) {
            $countryId = $country->getId();
        } else {
            $countryId = $country;
        }

        return $this->addFieldToFilter('country_id', $countryId);
    }
}
