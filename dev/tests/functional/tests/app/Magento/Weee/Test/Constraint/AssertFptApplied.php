<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Weee\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertFptApplied
 * Checks that prices with fpt on category, product and cart pages are equal to specified in dataset
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
        \PHPUnit_Framework_Assert::assertEquals($prices, $actualPrices, 'Arrays should be equal');
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
        $productName = $product->getName();
        $this->openCategory($product);
        $actualPrices = [];
        $actualPrices = $this->getCategoryPrice($productName, $actualPrices);
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
        $actualPrices = $this->addToCart($product, $actualPrices);
        $actualPrices = $this->getCartPrice($product, $actualPrices);
        return $actualPrices;
    }

    /**
     * Open product category
     *
     * @param CatalogProductSimple $product
     * @return void
     */
    protected function openCategory(CatalogProductSimple $product)
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenu()->selectCategoryByName($product->getCategoryIds()[0]);
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
        $actualPrices['category_price'] =
            $this->catalogCategoryView
                ->getListProductBlock()->getProductPriceBlock($productName)->getEffectivePrice();
        $actualPrices['fpt_category'] =
            $this->catalogCategoryView->getListProductBlock()
                ->getProductFptBlock($productName, $this->fptLabel)->getFpt();
        $actualPrices['fpt_total_category'] =
            $this->catalogCategoryView->getListProductBlock()
                ->getProductFptBlock($productName, $this->fptLabel)->getFptTotal();
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
        $this->catalogProductView->getViewBlock()->fillOptions($product);
        $actualPrices['product_page_price'] =
            $this->catalogProductView->getViewBlock()->getPriceBlock()->getEffectivePrice();
        $actualPrices['product_page_fpt'] =
            $this->catalogProductView->getViewBlock()->getFptBlock($this->fptLabel)->getFpt();
        $actualPrices['product_page_fpt_total'] =
            $this->catalogProductView->getViewBlock()->getFptBlock($this->fptLabel)->getFptTotal();
        $this->catalogProductView->getViewBlock()->clickAddToCart();
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
        $actualPrices['cart_item_price'] =
            $this->checkoutCart->getCartBlock()->getCartItem($product)->getPrice();
        $actualPrices['cart_item_fpt'] =
            $this->checkoutCart->getCartBlock()->getCartItem($product)->getPriceFptBlock()->getFpt();
        $actualPrices['cart_item_fpt_total'] =
            $this->checkoutCart->getCartBlock()->getCartItem($product)->getPriceFptBlock()->getFptTotal();
        $actualPrices['cart_item_subtotal'] =
            $this->checkoutCart->getCartBlock()->getCartItem($product)->getSubtotalPrice();
        $actualPrices['cart_item_subtotal_fpt'] =
            $this->checkoutCart->getCartBlock()->getCartItem($product)->getSubtotalFptBlock()->getFpt();
        $actualPrices['cart_item_subtotal_fpt_total'] =
            $this->checkoutCart->getCartBlock()->getCartItem($product)->getSubtotalFptBlock()->getFptTotal();
        $actualPrices['grand_total'] =
            $this->checkoutCart->getTotalsBlock()->getGrandTotal();
        $actualPrices['total_fpt'] =
            $this->checkoutCart->getTotalsBlock()->getFptBlock()->getTotalFpt();
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
