<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Show Address Model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Config_Backend_Show_Address
    extends Mage_Customer_Model_Config_Backend_Show_Customer
{
    /**
     * Retrieve attribute objects
     *
     * @return array
     */
    protected function _getAttributeObjects()
    {
        $result = parent::_getAttributeObjects();
        $result[] = Mage::getSingleton('Magento_Eav_Model_Config')->getAttribute('customer_address', $this->_getAttributeCode());
        return $result;
    }
}
