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
 * Product stock qty block for abstract composite product
 *
 * @category   Magento
 * @package    Magento_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Block\Stockqty;

abstract class Composite extends \Magento\CatalogInventory\Block\Stockqty\DefaultStockqty
{
    /**
     * Child products cache
     *
     * @var array
     */
    private $_childProducts;

    /**
     * Retrieve child products
     *
     * @return array
     */
    abstract protected function _getChildProducts();

    /**
     * Retrieve child products (using cache)
     *
     * @return array
     */
    public function getChildProducts()
    {
        if ($this->_childProducts === null) {
            $this->_childProducts = $this->_getChildProducts();
        }
        return $this->_childProducts;
    }

    /**
     * Retrieve product stock qty
     *
     * @return float
     */
    public function getProductStockQty($product)
    {
        return $product->getStockItem()->getStockQty();
    }

    /**
     * Retrieve id of details table placeholder in template
     *
     * @return string
     */
    public function getDetailsPlaceholderId()
    {
        return $this->getPlaceholderId() . '-details';
    }
}
