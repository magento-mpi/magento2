<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Directory country format resource model
 *
 * @category    Magento
 * @package     Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Directory_Model_Resource_Country_Format_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Directory_Model_Country_Format', 'Magento_Directory_Model_Resource_Country_Format');
    }

    /**
     * Set country filter
     *
     * @param string|Magento_Directory_Model_Country $country
     * @return Magento_Directory_Model_Resource_Country_Format_Collection
     */
    public function setCountryFilter($country)
    {
        if ($country instanceof Magento_Directory_Model_Country) {
            $countryId = $country->getId();
        } else {
            $countryId = $country;
        }

        return $this->addFieldToFilter('country_id', $countryId);
    }
}
