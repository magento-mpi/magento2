<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Reports\Test\Page\Adminhtml\ProductReportReview;
use Magento\Reports\Test\Page\Adminhtml\CustomerReportReview;
use Magento\Review\Test\Constraint\AssertProductReviewsReportByCustomerGrid;

/**
 * Test Creation for Customer Review ReportEntity
 *
 * Preconditions:
 * 1. Create customer
 * 2. Create simple product
 * 3. Open Product created in preconditions
 * 4. Click "Be the first to review this product "
 * 5. Fill data according to DataSet
 * 6. Click Submit review
 *
 * Test Flow:
 * 1. Open Reports -> Review : By Customers
 * 2. Assert Reviews qty
 * 3. Click Show Reviews
 * 4. Perform appropriate assertions.
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-27555
 */
class CustomerReviewReportEntityTest extends Injectable
{
    /**
     * Factory for fixture
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Customer frontend logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Product reviews report page
     *
     * @var ProductReportReview
     */
    protected $productReportReview;

    /**
     * Frontend product view page
     *
     * @var CatalogProductView
     */
    protected $pageCatalogProductView;

    /**
     * Cms Index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Catalog Category page
     *
     * @var CatalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Customer frontend login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Customer fixture
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
        $customer = $this->fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'johndoe_unique']);
        $customer->persist();
        $this->customer = $customer;
    }

    /**
     * Preparing pages for test
     *
     * @param ProductReportReview $productReportReview
     * @param CatalogProductView $pageCatalogProductView
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __inject(
        ProductReportReview $productReportReview,
        CatalogProductView $pageCatalogProductView,
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->productReportReview = $productReportReview;
        $this->pageCatalogProductView = $pageCatalogProductView;
        $this->cmsIndex = $cmsIndex;
        $this->catalogCategoryView = $catalogCategoryView;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Test Creation for Customer Review ReportEntity
     *
     * @param ReviewInjectable $review
     * @param AssertProductReviewsReportByCustomerGrid $assertProductReviewsReportByCustomerGrid
     * @param CustomerReportReview $customerReportReview
     * @param $customerLogin
     * @param $reviewsCount
     * @return array
     */
    public function test(
        ReviewInjectable $review,
        AssertProductReviewsReportByCustomerGrid $assertProductReviewsReportByCustomerGrid,
        CustomerReportReview $customerReportReview,
        $customerLogin,
        $reviewsCount
    ) {
        // Preconditions
        /** @var CatalogProductSimple $product */
        $product = $this->fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => 'product_with_category']);
        $product->persist();
        $this->cmsIndex->open();
        if (!$this->cmsIndex->getLinksBlock()->isLinkVisible('Log Out') && $customerLogin == 'Yes') {
            $this->cmsIndex->getLinksBlock()->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($this->customer);
        }
        $categoryName = $product->getCategoryIds()[0];
        $productName = $product->getName();
        $this->cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
        $this->pageCatalogProductView->getViewBlock()->clickAddReview();
        $this->pageCatalogProductView->getReviewFormBlock()->fill($review);
        $this->pageCatalogProductView->getReviewFormBlock()->submit();
        $customerName = $this->customer->getFirstName() . ' ' . $this->customer->getLastName();
        // Steps
        $customerReportReview->open();
        $assertProductReviewsReportByCustomerGrid->processAssert($customerReportReview, $customerName, $reviewsCount);
        $customerReportReview->getGridBlock()->openReview($customerName);

        return ['product' => $product, 'review' => $review];
    }

    /**
     * Logout customer from frontend account
     *
     * return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
