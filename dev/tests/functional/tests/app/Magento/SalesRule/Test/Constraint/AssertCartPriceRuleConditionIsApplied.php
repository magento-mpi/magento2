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
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertCartPriceRuleConditionIsApplied
 *
 * 1. Navigate to frontend
 * 2. If "isLoggedIn" not empty
 *    - login as customer
 * 3. Add test product(s) to shopping cart with specify quantity
 * 4. If "salesRule/data/coupon_code" not empty:
 *    - fill "Enter your code" input in Dіscount Codes
 *    - click "Apply Coupon" button
 * 5. If "address/data/country_id" not empty:
 *    On Estimate Shipping and Tax:
 * 	  - fill Country, State/Province, Zip/Postal Code
 *    - click 'Get a Quote' button
 * 	  - select 'Flat Rate' shipping
 * 	  - click 'Update Total' button
 * 6. Check that shopping cart subtotal not equals with grand total(excluding shipping price if exist).
 */
class AssertCartPriceRuleConditionIsApplied extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

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
     * Assert that shopping cart subtotal not equals with grand total
     *
     * @param CheckoutCart $checkoutCart
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
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
        CatalogCategoryView $catalogCategoryView,
        CatalogProductView $catalogProductView,
        SalesRuleInjectable $salesRule,
        AddressInjectable $address,
        $productQuantity,
        $shipping,
        $isLoggedIn,
        FixtureInterface $customer,
        FixtureInterface $productForSalesRule1,
        FixtureInterface $productForSalesRule2
    ) {
        $this->checkoutCart = $checkoutCart;
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->catalogCategoryView = $catalogCategoryView;
        $this->catalogProductView = $catalogProductView;
        $this->customer = $customer;
        $this->productForSalesRule1 = $productForSalesRule1;
        $this->productForSalesRule2 = $productForSalesRule2;

        $cmsIndex->open();
        if ($isLoggedIn) {
            $this->login();
        }
        if ($productQuantity) {
            $this->addProductsToCart($productQuantity);
        }
        if ($salesRule->getCouponCode()) {
            $this->checkoutCart->getDiscountCodesBlock()->applyCouponCode($salesRule->getCouponCode());
        }
        if ($address->hasData('country_id')) {
            $this->checkoutCart->getShippingBlock()->fillEstimateShippingAndTax($address);
            $this->checkoutCart->getShippingBlock()->selectShippingMethod($shipping);
        }
        $checkoutCart->open();
        $grandTotal = $checkoutCart->getTotalsBlock()->getGrandTotal();
        $subTotal = $checkoutCart->getTotalsBlock()->getSubtotal();
        if($checkoutCart->getTotalsBlock()->isVisibleShippingPriceBlock()){
            preg_match('/\$(.*)$/',$grandTotal, $grandTotalMatch);
            preg_match('/\$(.*)$/',$checkoutCart->getTotalsBlock()->getChippingPrice(), $shippingPriceMatch);
            $grandTotal = number_format(($grandTotalMatch[1] - $shippingPriceMatch[1]), 2);
            $grandTotal = '$'.$grandTotal;
        }
        \PHPUnit_Framework_Assert::assertNotEquals(
            $subTotal, $grandTotal,
            'Shopping cart subtotal: \'' . $subTotal
                . '\' equals with grand total: \'' . $grandTotal . '\''
        );
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
        foreach($productQuantity as $product => $quantity){
            if ($quantity > 0) {
                $categoryName = $this->$product->getDataFieldConfig('category_ids')['source']
                    ->getCategory()['0']->getData('name');
                $productName = $this->$product->getName();
                $this->cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
                $this->catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
                $this->catalogProductView->getViewBlock()->setQtyAndClickAddToCart($quantity);
            }
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Shopping cart subtotal not equals with grand total - price rule condition is applied.';
    }
}
