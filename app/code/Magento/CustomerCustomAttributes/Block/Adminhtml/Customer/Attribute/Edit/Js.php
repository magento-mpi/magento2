<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer and Customer Address Attributes Edit JavaScript Block
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Attribute_Edit_Js
    extends Magento_Adminhtml_Block_Template
{
    /**
     * Retrieve allowed Input Validate Filters in JSON format
     *
     * @return string
     */
    public function getValidateFiltersJson()
    {
        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode
            (Mage::helper('Magento_CustomerCustomAttributes_Helper_Data')->getAttributeValidateFilters()
        );
    }

    /**
     * Retrieve allowed Input Filter Types in JSON format
     *
     * @return string
     */
    public function getFilteTypesJson()
    {
        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode(
            Mage::helper('Magento_CustomerCustomAttributes_Helper_Data')->getAttributeFilterTypes()
        );
    }

    /**
     * Returns array of input types with type properties
     *
     * @return array
     */
    public function getAttributeInputTypes()
    {
        return Mage::helper('Magento_CustomerCustomAttributes_Helper_Data')->getAttributeInputTypes();
    }
}
