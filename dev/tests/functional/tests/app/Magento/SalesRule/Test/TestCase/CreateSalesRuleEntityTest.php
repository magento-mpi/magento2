<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteNew;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteEdit;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Fixture\FixtureFactory;
use Magento\Customer\Test\Fixture\AddressInjectable;

/**
 * Test Creation for Create SalesRuleEntity
 *
 * Test Flow:
 * Precondition:
 * 1. 2 sub categories in Default Category are created.
 * 2. 2 simple products are created and assigned to different subcategories by one for each
 * 3. Default customer are created
 * 4. Clear shopping cart
 *
 * Steps:
 * 1. Login to backend as admin
 * 2. Navigate to MARKETING->Cart Price Rule
 * 3. Create Cart Price rule according to dataset and click "Save" button
 * 4. Perform assets
 * 5. Navigate to frontend
 * 6. If "isLoggedIn" not empty
 *    - login as customer
 * 7. Add test product(s) to shopping cart with specify quantity
 * 8. If "salesRule/data/coupon_code" not empty:
 *    - fill "Enter your code" input in Dіscount Codes
 *    - click "Apply Coupon" button
 * 9. If "address/data/country_id" not empty:
 *    On Estimate Shipping and Tax:
 * 	  - fill Country, State/Province, Zip/Postal Code
 *    - click 'Get a Quote' button
 * 	  - select 'Flat Rate' shipping
 * 	  - click 'Update Total' button
 * 10. Perform asserts
 *
 * @group Shopping_Cart_Price_Rules_(MX)
 * @ZephyrId MTA-73
 */
class CreateSalesRuleEntityTest extends Injectable
{
    /**
     * @var PromoQuoteNew
     */
    protected $promoQuoteNew;

    /**
     * @var PromoQuoteEdit
     */
    protected $promoQuoteEdit;

    /**
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * @var CatalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * @var PromoQuoteIndex
     */
    protected $promoQuoteIndex;

    /**
     * @var \Magento\Customer\Test\Fixture\CustomerInjectable
     */
    protected $customer;

    /**
     * @var \Magento\Catalog\Test\Fixture\CatalogProductSimple
     */
    protected $productForSalesRule1;

    /**
     * @var \Magento\Catalog\Test\Fixture\CatalogProductSimple
     */
    protected $productForSalesRule2;

    /**
     * Sales rule name
     *
     * @var string
     */
    protected $salesRuleName;

    /**
     * isloggedIn value from data set
     *
     * @var int
     */
    protected $isLoggedIn;

    /**
     * @param PromoQuoteNew $promoQuoteNew
     * @param PromoQuoteIndex $promoQuoteIndex
     * @param PromoQuoteEdit $promoQuoteEdit
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     */
    public function __inject(
        PromoQuoteNew $promoQuoteNew,
        PromoQuoteIndex $promoQuoteIndex,
        PromoQuoteEdit $promoQuoteEdit,
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart
    ) {
        $this->promoQuoteNew = $promoQuoteNew;
        $this->promoQuoteIndex = $promoQuoteIndex;
        $this->promoQuoteEdit = $promoQuoteEdit;
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->catalogCategoryView = $catalogCategoryView;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
    }

    /**
     * @param FixtureFactory $fixtureFactory
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->customer = $fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $this->customer->persist();

        $this->productForSalesRule1 = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'simple_for_salesrule_1']);
        $this->productForSalesRule1->persist();

        $this->productForSalesRule2 = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'simple_for_salesrule_2']);
        $this->productForSalesRule2->persist();
    }

    /**
     * @param SalesRuleInjectable $salesRule
     * @param AddressInjectable $address
     * @param array $productQuantity
     * @param array $shipping
     * @param int $isLoggedIn
     */
    public function testCreateSalesRule(
        SalesRuleInjectable $salesRule,
        AddressInjectable $address,
        $productQuantity,
        $shipping,
        $isLoggedIn
    ) {
        // Preconditions
        $this->checkoutCart->open()->getCartBlock()->clearShoppingCart();
        $this->salesRuleName = $salesRule->getName();
        $this->isLoggedIn = $isLoggedIn;

        // Steps
        $this->promoQuoteNew->open();
        $this->promoQuoteNew->getSalesRuleForm()->fill($salesRule);
        $this->promoQuoteNew->getFormPageActions()->save();
        $this->cmsIndex->open();
        if($isLoggedIn){
            $this->login();
        }
        $this->addProductsToCart($productQuantity);

        if($salesRule->getData('coupon_code')){
            $this->checkoutCart->getDiscountCodesBlock()->enterCodeAndClickApply($salesRule->getData('coupon_code'));
        }
        if($address->getData('country_id')){
            $this->checkoutCart->getShippingBlock()->fillShippingAddress($address);
            $this->checkoutCart->getShippingBlock()->selectShippingMethod($shipping);
        }
    }

    /**
     * LogIn customer
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
     */
    protected function addProductsToCart($productQuantity)
    {
        foreach($productQuantity as $product => $quantity){
            if($quantity > 0){
                $categoryName = $this->$product->getDataFieldConfig('category_ids')['fixture']->getCategory()->getData('fields/name/value');
                $productName = $this->$product->getName();
                $this->cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
                $this->catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
                $this->catalogProductView->getViewBlock()->setQtyAndClickAddToCart($quantity);
            }
        }
    }

    /**
     * Delete current sales rule and logout customer from frontend account
     *
     * return void
     */
    public function tearDown()
    {
        $filter = [
            'name' => $this->salesRuleName,
        ];

        $this->promoQuoteIndex->open();
        $this->promoQuoteIndex->getPromoQuoteGrid()->searchAndOpen($filter);
        $this->promoQuoteEdit->getFormPageActions()->delete();
        if($this->isLoggedIn){
            $this->customerAccountLogout->open();
        }
    }
}
