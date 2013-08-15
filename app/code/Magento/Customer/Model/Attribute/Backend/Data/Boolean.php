<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Boolean customer attribute backend model
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Attribute_Backend_Data_Boolean
    extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Prepare data before attribute save
     *
     * @param Magento_Customer_Model_Customer $customer
     * @return Magento_Customer_Model_Attribute_Backend_Data_Boolean
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
