<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Banner\Test\Constraint;

use Magento\Banner\Test\Fixture\BannerInjectable;
use Magento\Banner\Test\Page\Adminhtml\BannerIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertBannerNotInGrid
 * Assert that deleted banner is absent in grid and can't be found by name
 */
class AssertBannerNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

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
