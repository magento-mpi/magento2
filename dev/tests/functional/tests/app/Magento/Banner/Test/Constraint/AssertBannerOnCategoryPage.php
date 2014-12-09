<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Constraint;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertBannerOnCategoryPage
 * Check that banner presents on specific category page
 */
class AssertBannerOnCategoryPage extends AbstractConstraint
{
    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that banner presents on specific category page
     *
     * @param CatalogProductSimple $product
     * @param CmsIndex $cmsIndex
     * @param BannerInjectable $banner
     * @param CatalogCategoryView $catalogCategoryView
     * @param CustomerInjectable $customer[optional]
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        CmsIndex $cmsIndex,
        BannerInjectable $banner,
        CatalogCategoryView $catalogCategoryView,
        CustomerInjectable $customer = null
    ) {
        if ($customer !== null) {
            $this->objectManager->create(
                'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
                ['customer' => $customer]
            )->run();
        } else {
            $cmsIndex->open();
        }
        $cmsIndex->getTopmenu()->selectCategoryByName($product->getCategoryIds()[0]);
        \PHPUnit_Framework_Assert::assertTrue(
            $catalogCategoryView->getBannerViewBlock()->checkWidgetBanners($banner, $customer),
            'Banner is absent on Category page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Banner is presents on Category page.";
    }
}
