<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Weee\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Checks that prices with fpt on category, product and cart pages are equal to specified in dataset.
 */
class AssertFptApplied extends AbstractConstraint
{
    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Catalog product page
     *
     * @var catalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Catalog product page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Catalog product page
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Fpt label
     *
     * @var string
     */
    protected $fptLabel;

    /**
     * Assert that specified prices with fpt are actual on category, product and cart pages
     *
     * @param CatalogProductSimple $product
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param array $prices
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        array $prices
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->catalogCategoryView = $catalogCategoryView;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
        $this->fptLabel = $product->getDataFieldConfig('attribute_set_id')['source']
            ->getAttributeSet()->getDataFieldConfig('assigned_attributes')['source']
            ->getAttributes()[0]->getFrontendLabel();
        $this->clearShoppingCart();
        $actualPrices = $this->getPrices($product);
        //Prices verification
        \PHPUnit_Framework_Assert::assertEquals(
            $prices,
            $actualPrices,
            'Prices on front should be equal to defined in dataset'
        );
    }

    /**
     * Clear shopping cart
     *
     * @return void
     */
    protected function clearShoppingCart()
    {
        $this->checkoutCart->open();
        $this->checkoutCart->getCartBlock()->clearShoppingCart();
    }

    /**
     * Get prices with fpt on category, product and cart pages
     *
     * @param CatalogProductSimple $product
     * @return array
     */
    protected function getPrices(CatalogProductSimple $product)
    {
        $actualPrices = [];
        $productName = $product->getName();
        // Get prices with fpt on category page
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenu()->selectCategoryByName($product->getCategoryIds()[0]);
        $actualPrices = $this->getCategoryPrice($productName, $actualPrices);
        // Get prices with fpt on product page
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
        $actualPrices = $this->addToCart($product, $actualPrices);
        // Get prices with fpt on cart page
        $actualPrices = $this->getCartPrice($product, $actualPrices);

        return $actualPrices;
    }

    /**
     * Get prices on category page
     *
     * @param string $productName
     * @param array $actualPrices
     * @return array
     */
    protected function getCategoryPrice($productName, $actualPrices)
    {
        $productBlock = $this->catalogCategoryView->getListProductBlock();
        $actualPrices['category_price'] = $productBlock->getProductPriceBlock($productName)->getEffectivePrice();
        $productWeeeBlock = $this->catalogCategoryView->getWeeeListProductBlock();
        $actualPrices['fpt_category'] = $productWeeeBlock->getProductFptBlock($productName, $this->fptLabel)->getFpt();
        $actualPrices['fpt_total_category'] = $productWeeeBlock->getProductFptBlock($productName, $this->fptLabel)
            ->getFptTotal();

        return $actualPrices;
    }

    /**
     * Fill options get price and add to cart
     *
     * @param CatalogProductSimple $product
     * @param array $actualPrices
     * @return array
     */
    protected function addToCart(CatalogProductSimple $product, array $actualPrices)
    {
        $viewBlock = $this->catalogProductView->getViewBlock();
        $viewBlock->fillOptions($product);
        $actualPrices['product_page_price'] = $viewBlock->getPriceBlock()->getEffectivePrice();
        $viewWeeeBlock = $this->catalogProductView->getWeeeViewBlock();
        $actualPrices['product_page_fpt'] = $viewWeeeBlock->getFptBlock($this->fptLabel)->getFpt();
        $actualPrices['product_page_fpt_total'] = $viewWeeeBlock->getFptBlock($this->fptLabel)->getFptTotal();
        $viewBlock->clickAddToCart();

        return $actualPrices;
    }

    /**
     * Get cart prices
     *
     * @param CatalogProductSimple $product
     * @param array $actualPrices
     * @return array
     */
    protected function getCartPrice(CatalogProductSimple $product, array $actualPrices)
    {
        $productItem = $this->checkoutCart->getCartBlock()->getCartItem($product);
        $productWeeeItem = $this->checkoutCart->getWeeeCartBlock()->getCartItem($product);
        $actualPrices['cart_item_price'] = $productItem->getPrice();
        $actualPrices['cart_item_fpt'] = $productWeeeItem->getPriceFptBlock()->getFpt();
        $actualPrices['cart_item_fpt_total'] = $productWeeeItem->getPriceFptBlock()->getFptTotal();
        $actualPrices['cart_item_subtotal'] = $productItem->getSubtotalPrice();
        $actualPrices['cart_item_subtotal_fpt'] = $productWeeeItem->getSubtotalFptBlock()->getFpt();
        $actualPrices['cart_item_subtotal_fpt_total'] = $productWeeeItem->getSubtotalFptBlock()->getFptTotal();
        $actualPrices['grand_total'] = $this->checkoutCart->getTotalsBlock()->getGrandTotal();
        $actualPrices['total_fpt'] = $this->checkoutCart->getWeeeTotalsBlock()->getFptBlock()->getTotalFpt();

        return $actualPrices;
    }

    /**
     * Text of FPT is applied
     *
     * @return string
     */
    public function toString()
    {
        return 'FPT is applied to product.';
    }
}
