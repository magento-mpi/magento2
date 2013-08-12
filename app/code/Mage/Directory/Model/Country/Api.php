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
 * Directory Country Api
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Model_Country_Api extends Magento_Api_Model_Resource_Abstract
{
    /**
     * Retrieve countries list
     *
     * @return array
     */
    public function items()
    {
        $collection = Mage::getModel('Mage_Directory_Model_Country')->getCollection();

        $result = array();
        foreach ($collection as $country) {
            /* @var $country Mage_Directory_Model_Country */
            $country->getName(); // Loading name in default locale
            $result[] = $country->toArray(array('country_id', 'iso2_code', 'iso3_code', 'name'));
        }

        return $result;
    }
} // Class Mage_Directory_Model_Country_Api End
