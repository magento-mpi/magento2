<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Store\Test\Fixture\Store;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertStoreViewFrontend
 * Assert that created store view available on frontend (store view selector on page top)
 */
class AssertStoreViewFrontend extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created store view available on frontend (store view selector on page top)
     *
     * @param Store $store
     * @param CmsIndex $cmsIndex
     * @return void
     */
    public function processAssert(Store $store, CmsIndex $cmsIndex)
    {
        $cmsIndex->open();
        list($website, $storeGroup) = explode("/", $store->getGroupId());
        $storeCode = $store->getCode();
        if ($storeGroup != "Main Website Store") {
            $cmsIndex->getFooterBlock()->selectStoreGroup($storeGroup);
        }
        $isStoreViewVisible = $cmsIndex->getStoreSwitcherBlock()->isStoreViewVisible($storeCode);
        \PHPUnit_Framework_Assert::assertTrue(
            $isStoreViewVisible,
            "Store view is not visible in dropdown on CmsIndex page"
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Store view is visible in dropdown on CmsIndex page';
    }
}
