<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Sales_Model_Quote_Config
{
    /**
     * @var Magento_Catalog_Model_Attribute_Config
     */
    private $_attributeConfig;

    /**
     * @param Magento_Catalog_Model_Attribute_Config $attributeConfig
     */
    public function __construct(Magento_Catalog_Model_Attribute_Config $attributeConfig)
    {
        $this->_attributeConfig = $attributeConfig;
    }

    /**
     * @return array
     */
    public function getProductAttributes()
    {
        return $this->_attributeConfig->getAttributeNames('sales_quote_item');
    }
}
