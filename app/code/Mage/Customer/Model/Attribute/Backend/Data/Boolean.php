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
 * Boolean customer attribute backend model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Attribute_Backend_Data_Boolean
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare data before attribute save
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Customer_Model_Attribute_Backend_Data_Boolean
     */
    public function beforeSave($customer)
    {
        $attributeName = $this->getAttribute()->getName();
        $inputValue = $customer->getData($attributeName);
        $sanitizedValue = (!empty($inputValue)) ? '1' : '0';
        $customer->setData($attributeName, $sanitizedValue);
        return $this;
    }
}
