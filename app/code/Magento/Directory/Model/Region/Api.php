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
 * Directory Region Api
 *
 * @category   Magento
 * @package    Magento_Directory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Directory_Model_Region_Api extends Magento_Api_Model_Resource_Abstract
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
            $country = Mage::getModel('Magento_Directory_Model_Country')->loadByCode($country);
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
} // Class Magento_Directory_Model_Region_Api End
