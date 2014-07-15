<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\InjectableFixture;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Page\Product\CatalogProductCompare;

/**
 * Class AbstractCompareProductsTest
 * Abstract class for compare products class
 */
class AbstractCompareProductsTest extends Injectable
{
    /**
     * Array products
     *
     * @var array
     */
    protected $products;

    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Catalog product compare page
     *
     * @var CatalogProductCompare
     */
    protected $catalogProductCompare;

    /**
     * Catalog product page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Customer login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Fixture customer
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @param CustomerInjectable $customer
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory, CustomerInjectable $customer)
    {
        $this->fixtureFactory = $fixtureFactory;
        $customer->persist();
        $this->customer = $customer;
    }

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogProductView $catalogProductView
     * @param CustomerAccountLogin $customerAccountLogin
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CatalogProductView $catalogProductView,
        CustomerAccountLogin $customerAccountLogin
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->catalogProductView = $catalogProductView;
        $this->customerAccountLogin = $customerAccountLogin;
    }

    /**
     * Login customer
     *
     * @return void
     */
    protected function loginCustomer()
    {
        if (!$this->cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
            $this->cmsIndex->getLinksBlock()->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($this->customer);
        }
    }

    /**
     * Create products
     *
     * @param string $products
     * @return array
     */
    protected function createProducts($products)
    {
        $products = explode(',', $products);
        foreach ($products as &$product) {
            list($fixture, $dataSet) = explode('::', $product);
            $product = $this->fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            $product->persist();
        }
        return $products;
    }

    /**
     * Add products to compare list
     *
     * @param array $products
     * @param AbstractConstraint $assert
     * @return void
     */
    protected function addProducts(array $products, AbstractConstraint $assert = null)
    {
        foreach ($products as $itemProduct) {
            $this->catalogProductView->init($itemProduct);
            $this->catalogProductView->open();
            $this->catalogProductView->getViewBlock()->clickAddToCompare();
            if ($assert !== null) {
                $this->productCompareAssert($assert, $itemProduct);
            }
        }
    }

    /**
     * Perform assert
     *
     * @param AbstractConstraint $assert
     * @param InjectableFixture $product
     * @return void
     */
    protected function productCompareAssert(AbstractConstraint $assert, InjectableFixture $product)
    {
        $assert->configure(
            $this,
            ['catalogProductView' => $this->catalogProductView, 'product' => $product]
        );
        \PHPUnit_Framework_Assert::assertThat($this->getName(), $assert);
    }
}
