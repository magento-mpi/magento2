<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Helper;

/**
 * Catalog Inventory default helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Show out of stock config path
     */
    const XML_PATH_SHOW_OUT_OF_STOCK = 'cataloginventory/options/show_out_of_stock';

    /**
     * Auto return config path
     */
    const XML_PATH_ITEM_AUTO_RETURN = 'cataloginventory/item_options/auto_return';

    /**
     * Path to configuration option 'Display product stock status'
     */
    const XML_PATH_DISPLAY_PRODUCT_STOCK_STATUS = 'cataloginventory/options/display_product_stock_status';

    /**
     * Error codes, that Catalog Inventory module can set to quote or quote items
     */
    const ERROR_QTY = 1;

    /**
     * Error qty increments
     */
    const ERROR_QTY_INCREMENTS = 2;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
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
            'is_decimal_divided'
        );
    }

    /**
     * Display out of stock products option
     *
     * @return bool
     */
    public function isShowOutOfStock()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_OUT_OF_STOCK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if creditmemo items auto return option is enabled
     *
     * @return bool
     */
    public function isAutoReturnEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ITEM_AUTO_RETURN,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get 'Display product stock status' option value
     * Shows if it is necessary to show product stock status ('in stock'/'out of stock')
     *
     * @return bool
     */
    public function isDisplayProductStockStatus()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISPLAY_PRODUCT_STOCK_STATUS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
