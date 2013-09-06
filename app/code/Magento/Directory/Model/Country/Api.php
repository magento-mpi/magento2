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
 * Directory Country Api
 *
 * @category   Magento
 * @package    Magento_Directory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Directory_Model_Country_Api extends Magento_Api_Model_Resource_Abstract
{
    /**
     * Retrieve countries list
     *
     * @return array
     */
    public function items()
    {
        $collection = Mage::getModel('Magento_Directory_Model_Country')->getCollection();

        $result = array();
        foreach ($collection as $country) {
            /* @var $country Magento_Directory_Model_Country */
            $country->getName(); // Loading name in default locale
            $result[] = $country->toArray(array('country_id', 'iso2_code', 'iso3_code', 'name'));
        }

        return $result;
    }
} // Class Magento_Directory_Model_Country_Api End
