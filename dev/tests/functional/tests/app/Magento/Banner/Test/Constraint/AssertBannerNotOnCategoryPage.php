<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Constraint;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertBannerNotOnCategoryPage
 * Check that banner is absent on specific category page
 */
class AssertBannerNotOnCategoryPage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that banner is absent on specific category page
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
        \PHPUnit_Framework_Assert::assertFalse(
            $catalogCategoryView->getBannerViewBlock()->checkWidgetBanners($banner, $customer),
            'Banner is presents on Category page.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Banner is absent on Category page.";
    }
}
