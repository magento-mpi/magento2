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
 * \Directory Country Api
 *
 * @category   Magento
 * @package    Magento_Directory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Model\Country;

class Api extends \Magento\Api\Model\Resource\AbstractResource
{
    /**
     * Retrieve countries list
     *
     * @return array
     */
    public function items()
    {
        $collection = \Mage::getModel('Magento\Directory\Model\Country')->getCollection();

        $result = array();
        foreach ($collection as $country) {
            /* @var $country \Magento\Directory\Model\Country */
            $country->getName(); // Loading name in default locale
            $result[] = $country->toArray(array('country_id', 'iso2_code', 'iso3_code', 'name'));
        }

        return $result;
    }
} // Class \Magento\Directory\Model\Country\Api End
