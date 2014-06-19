<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertCartPriceRuleApplying
 * Abstract class for implementing assert applying
 */
abstract class AssertCartPriceRuleApplying extends AbstractConstraint
{
    /**
     * Page CheckoutCart
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Page CmsIndex
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Page CustomerAccountLogin
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Page CustomerAccountLogout
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Page CatalogCategoryView
     *
     * @var CatalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Page CatalogProductView
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Customer from precondition
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * First product from precondition
     *
     * @var CatalogProductSimple
     */
    protected $productForSalesRule1;

    /**
     * Second product from precondition
     *
     * @var CatalogProductSimple
     */
    protected $productForSalesRule2;

    /**
     * Implementation assert
     *
     * @return void
     */
    abstract protected function assert();

    /**
     * 1. Navigate to frontend
     * 2. If "Log Out" link is visible and "isLoggedIn" empty
     *    - makes logout
     * 3. If "isLoggedIn" not empty
     *    - login as customer
     * 4. Clear shopping cart
     * 5. Add test product(s) to shopping cart with specify quantity
     * 6. If "salesRule/data/coupon_code" not empty:
     *    - fill "Enter your code" input in Dіscount Codes
     *    - click "Apply Coupon" button
     * 7. If "address/data/country_id" not empty:
     *    On Estimate Shipping and Tax:
     *    - fill Country, State/Province, Zip/Postal Code
     *    - click 'Get a Quote' button
     *    - select 'Flat Rate' shipping
     *    - click 'Update Total' button
     * 8. Implementation assert
     *
     *
     * @param CheckoutCart $checkoutCart
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductView $catalogProductView
     * @param SalesRuleInjectable $salesRule
     * @param AddressInjectable $address
     * @param array $productQuantity
     * @param array $shipping
     * @param int $isLoggedIn
     * @param FixtureInterface $customer
     * @param FixtureInterface $productForSalesRule1
     * @param FixtureInterface $productForSalesRule2
     * @return void
     */
    public function processAssert(
        CheckoutCart $checkoutCart,
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductView $catalogProductView,
        FixtureInterface $customer,
        FixtureInterface $productForSalesRule1,
        FixtureInterface $productForSalesRule2,
        SalesRuleInjectable $salesRule,
        AddressInjectable $address,
        $productQuantity,
        $shipping,
        $isLoggedIn
    ) {
        $this->checkoutCart = $checkoutCart;
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->catalogCategoryView = $catalogCategoryView;
        $this->catalogProductView = $catalogProductView;
        $this->customer = $customer;
        $this->productForSalesRule1 = $productForSalesRule1;
        $this->productForSalesRule2 = $productForSalesRule2;

        $this->cmsIndex->open();
        if ($this->cmsIndex->getLinksBlock()->isLinkVisible('Log Out') && !$isLoggedIn) {
            $this->customerAccountLogout->open();
        } elseif ($isLoggedIn) {
            $this->login();
        }
        $this->checkoutCart->open()->getCartBlock()->clearShoppingCart();
        $this->addProductsToCart($productQuantity);
        if ($salesRule->getCouponCode()) {
            $this->checkoutCart->getDiscountCodesBlock()->applyCouponCode($salesRule->getCouponCode());
        }
        if ($address->hasData('country_id')) {
            $this->checkoutCart->getShippingBlock()->fillEstimateShippingAndTax($address);
            $this->checkoutCart->getShippingBlock()->selectShippingMethod($shipping);
        }
        $this->assert();
    }

    /**
     * LogIn customer
     *
     * @return void
     */
    protected function login()
    {
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($this->customer);
    }

    /**
     * Add products to cart
     *
     * @param array $productQuantity
     * @return void
     */
    protected function addProductsToCart(array $productQuantity)
    {
        foreach ($productQuantity as $product => $quantity) {
            if ($quantity > 0) {
                $categoryName = $this->$product->getCategoryIds()[0];
                $productName = $this->$product->getName();
                $this->cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
                $this->catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
                $this->catalogProductView->getViewBlock()->setQtyAndClickAddToCart($quantity);
            }
        }
    }
}
