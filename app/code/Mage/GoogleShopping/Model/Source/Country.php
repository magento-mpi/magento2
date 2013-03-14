<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Content Target country Source
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Model_Source_Country
{
    /**
     * Retrieve option array with allowed countries
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_allowed = Mage::getSingleton('Mage_GoogleShopping_Model_Config')->getAllowedCountries();
        $result = array();
        foreach ($_allowed as $iso => $info) {
            $result[] = array('value' => $iso, 'label' => $info['name']);
        }
        return $result;
    }
}
