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
namespace Magento\CatalogInventory\Helper;

class Data extends \Magento\App\Helper\AbstractHelper
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
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $_config;

    /**
     * Core store config
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
    ) {
        $this->_config = $config;
        $this->_storeConfig = $coreStoreConfig;
        parent::__construct($context);
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

            foreach ($this->_config->getAll() as $typeId => $typeConfig) {
                self::$_isQtyTypeIds[$typeId] = isset($typeConfig['is_qty']) ? $typeConfig['is_qty'] : false;
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
     * @return string[]
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
        return $this->_storeConfig->isSetFlag(self::XML_PATH_SHOW_OUT_OF_STOCK, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Check if creditmemo items auto return option is enabled
     *
     * @return bool
     */
    public function isAutoReturnEnabled()
    {
        return $this->_storeConfig->isSetFlag(self::XML_PATH_ITEM_AUTO_RETURN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get 'Display product stock status' option value
     * Shows if it is necessary to show product stock status ('in stock'/'out of stock')
     *
     * @return bool
     */
    public function isDisplayProductStockStatus()
    {
        return $this->_storeConfig->isSetFlag(self::XML_PATH_DISPLAY_PRODUCT_STOCK_STATUS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
