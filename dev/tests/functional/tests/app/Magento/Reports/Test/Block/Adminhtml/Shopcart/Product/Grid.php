<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Block\Adminhtml\Shopcart\Product;

use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Products in Carts Report grid
 */
class Grid extends AbstractGrid
{
    /**
     * Product name selector
     *
     * @var string
     */
    protected $productName = '//tr[td[contains(@class,"col-name")] and contains(.,"%s")]';

    /**
     * Product price selector
     *
     * @var string
     */
    protected $productPrice =  '//td[contains(@class,"col-price") and contains(.,"%s")]';

    /**
     * Product carts selector
     *
     * @var string
     */
    protected $productCarts =  '//td[contains(@class,"col-carts") and contains(.,"%d")]';

    /**
     * Check that product visible in grid
     *
     * @param CatalogProductSimple $product
     * @param string $carts
     * @return bool
     */
    public function isProductVisible(CatalogProductSimple $product, $carts)
    {
        $result = false;
        $productName = sprintf($this->productName, $product->getName());
        $productPrice = sprintf($this->productPrice, $product->getPrice());
        $productRow = $this->_rootElement->find($productName, Locator::SELECTOR_XPATH);
        if ($productRow->isVisible()) {
            if ($productRow->find($productPrice, Locator::SELECTOR_XPATH)->isVisible() &&
                $productRow->find(sprintf($this->productCarts, $carts), Locator::SELECTOR_XPATH)->isVisible()
            ) {
                $result = true;
            }
        }

        return $result;
    }
}
