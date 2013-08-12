<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quote address attribute frontend resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Quote_Address_Attribute_Frontend
    extends Magento_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    /**
     * Fetch totals
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return array
     */
    public function fetchTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $arr = array();
        
        return $arr;
    }
}
