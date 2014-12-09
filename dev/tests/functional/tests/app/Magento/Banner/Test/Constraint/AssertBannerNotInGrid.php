<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Banner\Test\Page\Adminhtml\BannerIndex;

/**
 * Class AssertBannerNotInGrid
 * Assert that deleted banner is absent in grid and can't be found by name
 */
class AssertBannerNotInGrid extends AbstractConstraint
{
    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that deleted banner is absent in grid and can't be found by name
     *
     * @param BannerInjectable $banner
     * @param BannerIndex $bannerIndex
     * @return void
     */
    public function processAssert(BannerInjectable $banner, BannerIndex $bannerIndex)
    {
        $bannerIndex->open();

        \PHPUnit_Framework_Assert::assertFalse(
            $bannerIndex->getGrid()->isRowVisible(['banner' => $banner->getName()]),
            'Banner is present in banner grid.'
        );
    }

    /**
     * Banner is absent in Banners grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Banner is absent in Banners grid.';
    }
}
