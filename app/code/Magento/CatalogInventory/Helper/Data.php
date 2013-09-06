<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalo
 */
class Magento_CatalogInventory_Helper_Data extends Magento_Core_Helper_Abstract
{
    const XML_PATH_SHOW_OUT_OF_STOCK    = 'cataloginventory/options/show_out_of_stock';
    const XML_PATH_ITEM_AUTO_RETURN     = 'cataloginventory/item_options/auto_return';
    /**
     * Path to configuration option 'Display product stock status'
     */
    const XML_PATH_DISPLAY_PRODUCT_STOCK_STATUS = 'cataloginventory/options/display_product_stock_status';

    /**
     * Error codes, that Catalog Inventory module can set to quote or quote items
     */
    const ERROR_QTY =               1;
    const ERROR_QTY_INCREMENTS =    2;

    /**
     * All product types registry in scope of quantity availability
     *
     * @var array
     */
    protected static $_isQtyTypeIds;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Check if quantity defined for specified product type
     *
     * @param string $productTypeId
     * @return bool
     */
    public function isQty($productTypeId)
    {
        $this->getIsQtyTypeIds();
        if (!isset(self::$_isQtyTypeIds[$productTypeId])) {
            return false;
        }
        return self::$_isQtyTypeIds[$productTypeId];
    }

    /**
     * Get all registered product type ids and if quantity is defined for them
     *
     * @param bool $filter
     * @return array
     */
    public function getIsQtyTypeIds($filter = null)
    {
        if (null === self::$_isQtyTypeIds) {
            self::$_isQtyTypeIds = array();
            $productTypesXml = $this->_coreConfig->getNode('global/catalog/product/type');
            foreach ($productTypesXml->children() as $typeId => $configXml) {
                self::$_isQtyTypeIds[$typeId] = (bool)$configXml->is_qty;
            }
        }
        if (null === $filter) {
            return self::$_isQtyTypeIds;
        }
        $result = self::$_isQtyTypeIds;
        foreach ($result as $key => $value) {
            if ($value !== $filter) {
                unset($result[$key]);
            }
        }
        return $result;
    }

    /**
     * Retrieve inventory item options (used in config)
     *
     * @return array
     */
    public function getConfigItemOptions()
    {
        return array(
            'min_qty',
            'backorders',
            'min_sale_qty',
            'max_sale_qty',
            'notify_stock_qty',
            'manage_stock',
            'enable_qty_increments',
            'qty_increments',
            'is_decimal_divided',
        );
    }

    /**
     * Display out of stock products option
     *
     * @return bool
     */
    public function isShowOutOfStock()
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_SHOW_OUT_OF_STOCK);
    }

    /**
     * Check if creditmemo items auto return option is enabled
     * @return bool
     */
    public function isAutoReturnEnabled()
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_ITEM_AUTO_RETURN);
    }

    /**
     * Get 'Display product stock status' option value
     * Shows if it is necessary to show product stock status ('in stock'/'out of stock')
     *
     * @return bool
     */
    public function isDisplayProductStockStatus()
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_DISPLAY_PRODUCT_STOCK_STATUS);
    }
}