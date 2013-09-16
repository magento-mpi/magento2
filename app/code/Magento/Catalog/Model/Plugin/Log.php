<?php
/**
 * Plugin for Magento_Log_Model_Resource_Log model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Plugin_Log
{
    /**
     * @var Magento_Catalog_Model_Product_Compare_Item
     */
    protected $_productCompareItem;

    /**
     * @param Magento_Catalog_Model_Product_Compare_Item $productCompareItem
     */
    public function __construct(Magento_Catalog_Model_Product_Compare_Item $productCompareItem)
    {
        $this->_productCompareItem = $productCompareItem;
    }

    /**
     * Catalog Product Compare Items Clean
     * after plugin for clean method
     *
     * @param Magento_Log_Model_Resource_Log $logResourceModel
     * @return Magento_Log_Model_Resource_Log
     */
    public function afterClean($logResourceModel)
    {
        $this->_productCompareItem->clean();
        return $logResourceModel;
    }
}