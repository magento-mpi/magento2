<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quote address attribute frontend resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Quote_Address_Attribute_Frontend
    extends Magento_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    /**
     * Fetch totals
     *
     * @param Magento_Sales_Model_Quote_Address $address
     * @return array
     */
    public function fetchTotals(Magento_Sales_Model_Quote_Address $address)
    {
        $arr = array();
        
        return $arr;
    }
}
