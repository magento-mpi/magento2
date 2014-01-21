<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Controller\Adminhtml\Product\Initialization;

class StockDataFilter
{
    /**
     * The greatest value which could be stored in CatalogInventory Qty field
     */
    const MAX_QTY_VALUE = 99999999.9999;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(\Magento\Core\Model\StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * Filter stock data
     *
     * @param array $stockData
     * @return array
     */
    public function filter(array $stockData)
    {
        if (!isset($stockData['use_config_manage_stock'])) {
            $stockData['use_config_manage_stock'] = 0;
        }

        if ($stockData['use_config_manage_stock'] == 1 && !isset($stockData['manage_stock'])) {
            $stockData['manage_stock'] = $this->storeManager->getStore()
                ->getConfig(\Magento\CatalogInventory\Model\Stock\Item::XML_PATH_MANAGE_STOCK);
        }
        if (isset($stockData['qty']) && (float)$stockData['qty'] > self::MAX_QTY_VALUE) {
            $stockData['qty'] = self::MAX_QTY_VALUE;
        }

        if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
            $stockData['min_qty'] = 0;
        }

        if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
            $stockData['is_decimal_divided'] = 0;
        }

        return $stockData;
    }
} 
