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
 * Directory Region Api
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Region_Api extends Magento_Api_Model_Resource_Abstract
{
    /**
     * Retrieve regions list
     *
     * @param string $country
     * @return array
     */
    public function items($country)
    {
        try {
            $country = Mage::getModel('Mage_Directory_Model_Country')->loadByCode($country);
        } catch (Magento_Core_Exception $e) {
            $this->_fault('country_not_exists', $e->getMessage());
        }

        if (!$country->getId()) {
            $this->_fault('country_not_exists');
        }

        $result = array();
        foreach ($country->getRegions() as $region) {
            $region->getName();
            $result[] = $region->toArray(array('region_id', 'code', 'name'));
        }

        return $result;
    }
} // Class Mage_Directory_Model_Region_Api End
