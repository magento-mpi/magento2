<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tab;

/**
 * Product inventory data
 */
class Inventory extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_template = 'catalog/product/tab/inventory.phtml';

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\CatalogInventory\Model\Source\Stock
     */
    protected $stock;

    /**
     * @var \Magento\CatalogInventory\Model\Source\Backorders
     */
    protected $backorders;

    /**
     * @var \Magento\CatalogInventory\Service\V1\StockItemService
     */
    protected $stockItemService;

    /**
     * @var \Magento\Catalog\Helper\Product\Inventory
     */
    protected $inventoryHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Model\Source\Backorders $backorders
     * @param \Magento\CatalogInventory\Model\Source\Stock $stock
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService
     * @param \Magento\Catalog\Helper\Product\Inventory $inventoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Model\Source\Backorders $backorders,
        \Magento\CatalogInventory\Model\Source\Stock $stock,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService,
        \Magento\Catalog\Helper\Product\Inventory $inventoryHelper,
        array $data = array()
    ) {
        $this->stock = $stock;
        $this->backorders = $backorders;
        $this->catalogData = $catalogData;
        $this->coreRegistry = $coreRegistry;
        $this->stockItemService = $stockItemService;
        $this->inventoryHelper = $inventoryHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getBackordersOption()
    {
        if ($this->catalogData->isModuleEnabled('Magento_CatalogInventory')) {
            return $this->backorders->toOptionArray();
        }

        return array();
    }

    /**
     * Retrieve stock option array
     *
     * @return array
     */
    public function getStockOption()
    {
        if ($this->catalogData->isModuleEnabled('Magento_CatalogInventory')) {
            return $this->stock->toOptionArray();
        }

        return array();
    }

    /**
     * Return current product instance
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    /**
     * Retrieve Catalog Inventory  Stock Item Model
     *
     * @return \Magento\CatalogInventory\Service\V1\Data\StockItem
     */
    public function getStockItemDo()
    {
        return $this->stockItemService->getStockItem($this->getProduct()->getId());
    }

    /**
     * @param string $field
     * @return string|null
     */
    public function getFieldValue($field)
    {
        return $this->inventoryHelper->getFieldValue($field, $this->getStockItemDo());
    }

    /**
     * @param string $field
     * @return string|null
     */
    public function getConfigFieldValue($field)
    {
        return $this->inventoryHelper->getConfigFieldValue($field, $this->getStockItemDo());
    }

    /**
     * @param string $field
     * @return string|null
     */
    public function getDefaultConfigValue($field)
    {
        return $this->inventoryHelper->getDefaultConfigValue($field);
    }

    /**
     * Is readonly stock
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getProduct()->getInventoryReadonly();
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        if ($this->getProduct()->getId()) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getFieldSuffix()
    {
        return 'product';
    }

    /**
     * Check Whether product type can have fractional quantity or not
     *
     * @return bool
     */
    public function canUseQtyDecimals()
    {
        return $this->getProduct()->getTypeInstance()->canUseQtyDecimals();
    }

    /**
     * Check if product type is virtual
     *
     * @return bool
     */
    public function isVirtual()
    {
        return $this->getProduct()->getIsVirtual();
    }

    /**
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
