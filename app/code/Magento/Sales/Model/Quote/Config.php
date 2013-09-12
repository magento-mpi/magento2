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
     * @var Magento_Core_Model_Event_Manager
     */
    private $_eventManager;

    /**
     * @param Magento_Catalog_Model_Attribute_Config $attributeConfig
     * @param Magento_Core_Model_Event_Manager $eventManager
     */
    public function __construct(
        Magento_Catalog_Model_Attribute_Config $attributeConfig,
        Magento_Core_Model_Event_Manager $eventManager
    ) {
        $this->_attributeConfig = $attributeConfig;
        $this->_eventManager = $eventManager;
    }

    /**
     * @return array
     */
    public function getProductAttributes()
    {
        $attributes = $this->_attributeConfig->getAttributeNames('sales_quote_item');
        $transport = new Magento_Object();
        foreach ($attributes as $attributeCode) {
            $transport->setData($attributeCode, true);
        }
        $this->_eventManager->dispatch('sales_quote_config_get_product_attributes', array('attributes' => $transport));
        $result = array_keys($transport->getData());
        return $result;
    }
}
